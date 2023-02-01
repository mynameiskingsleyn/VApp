import { Component, Input, Inject } from '@angular/core';
import { I18nService } from '../shared/service/i18n.service';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';
import { XmlSearchService } from '../shared/service/xml.search.service';
import { XmlEditService } from './../shared/service/xml.edit.service';
import * as _ from "lodash";

@Component({
    selector: 'viewxml',
    templateUrl: './view.xml.html'
})
export class ViewXml {
    public dataLoaded = false;
    public messages: any = {};
    public getXmlFileResult: any = {"data" : {}};

    public selectedExterior: any;
    public selectedLayerAngle: any;

    public submitFlag: any = false;
    public viewFailedFiles: any = false;

    public allFailEditedFiles: any = [];

    public filesEdited: any = false;

    public isLoading: boolean;

    public  errorMessage: string = "";

    public miniErrorMessage: string = "";

    public finalDataForViewEdit: any = {};
    public editConfirmationStatus: any = {};

    @Input() public selectedStandard: any;
    @Input() public selectedLayer: any;
    @Input() public searchCriteria: any = {};
    @Input() public layerFiles: any = {};
    @Input() public standardRawXml: any = "";
    @Input() public layerRawXml: any = "";

    constructor(public i18nService: I18nService,
                private applicationConfiguration: ApplicationConfiguration,
                private xmlSearchService: XmlSearchService,
                private xmlEditService: XmlEditService) {

    }

    ngOnInit() {
        if (this.dataLoaded == false) {
            this.loadRequiredData();
        }
        this.filesEdited = false;
        this.isLoading = false;
        this.madeEdits();
    }

    async loadRequiredData() {
        this.searchCriteria = this.applicationConfiguration.getSearchCriteria();
        this.getXmlFileResult = this.applicationConfiguration.getXmlEditRequest();
        this.messages = this.applicationConfiguration.getMessages();
        let fileData = this.getXmlFileResult['data'];
        if(!this.applicationConfiguration.isEmpty(fileData)){
            for (var standardFile of this.getXmlFileResult["data"]["StandardFiles"]) {
                this.selectedStandard = standardFile["name"] + ':' + standardFile["exterior"];
                this.selectedExterior = standardFile["exterior"];

                this.layerFiles = standardFile["layerFiles"];
                this.standardRawXml = this.contentCatch(standardFile["rawXml"]);
                for (var layerFile of this.layerFiles) {
                    this.selectedLayer = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                    this.selectedLayerAngle = layerFile["layer_angle"];

                    this.layerRawXml = this.contentCatch(layerFile["rawXml"]);
                    break;
                }
                break;
            }

            this.dataLoaded = true;
        }

    }

    async showStandardXml() {
        this.resetMessages();
        let split = this.selectedStandard.split(':');
        this.selectedExterior = parseInt(split[1]);
        for (var standardFile of this.getXmlFileResult["data"]["StandardFiles"]) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            if (this.selectedStandard == checkFile) {
                this.layerFiles = standardFile["layerFiles"];
                this.standardRawXml = this.contentCatch(standardFile["rawXml"]);

                for (var layerFile of this.layerFiles) {
                    this.selectedLayer = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                    this.selectedLayerAngle = layerFile["layer_angle"];
                    let lRawXml = (layerFile["rawXml"].length > 5) ? layerFile["rawXml"] : "";
                    this.layerRawXml = lRawXml;
                    break;
                }
                break;
            }
        }
    }

    async showLayerXml() {
        let split = this.selectedLayer.split(':');
        this.selectedLayerAngle = split[2];
        for (var layerFile of this.layerFiles) {
            let checkFile = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
            if (this.selectedLayer == checkFile) {
                this.layerRawXml = this.contentCatch(layerFile["rawXml"]);
                break;
            }
        }
    }

    async refreshRawXml() {
        this.resetMessages();
        this.getXmlFileResult = this.applicationConfiguration.getResetXmlEditRequest();
        for (var standardFile of this.getXmlFileResult["data"]["StandardFiles"]) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            let json = {"jsonXml" : standardFile["jsonXml"]};
            let stdResponse = await this.xmlSearchService.getRawXml(json);
            standardFile["rawXml"] = stdResponse["rawXml"];
            standardFile["edited"] = false;
            if (this.selectedStandard == checkFile) {
                this.standardRawXml = this.contentCatch(standardFile["rawXml"]);
                this.layerFiles = standardFile["layerFiles"];
            }

            for (var layerFile of standardFile["layerFiles"]) {
                let checkLayerFile = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                let jsonLayer = {"jsonXml" : layerFile["jsonXml"]};
                let layerResponse = await this.xmlSearchService.getRawXml(jsonLayer);
                layerFile["rawXml"] = layerResponse["rawXml"];
                layerFile['edited'] = false;
                if (this.selectedLayer == checkLayerFile) {
                    this.layerRawXml = this.contentCatch(layerFile["rawXml"]);

                }
            }
        }
    }

    public setStandardEdited() {
        this.resetMessages();
        this.madeEdits();
        let split = this.selectedStandard.split(':');
        this.selectedExterior = parseInt(split[1]);
        for (var standardFile of this.getXmlFileResult["data"]["StandardFiles"]) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            if (this.selectedStandard == checkFile) {
                standardFile["edited"] = true;
                standardFile["rawXml"] = this.standardRawXml;
                break;
            }
        }
    }

    public setLayerEdited() {
        this.resetMessages();
        this.madeEdits();
        let split = this.selectedLayer.split(':');
        this.selectedLayerAngle = split[2];
        for (var layerFile of this.layerFiles) {
            let checkFile = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
            if (this.selectedLayer == checkFile) {
                layerFile["rawXml"] = this.layerRawXml;
                layerFile["edited"] = true;
                break;
            }
        }
    }

    async submitXml() {
        this.resetMessages();
        this.madeEdits();
        this.isLoading = true;
        let minLen = 5;
        let editFailFlag = false;
        this.allFailEditedFiles = [];
        let weHaveEmpty = false;
        for (var standardFile of this.getXmlFileResult["data"]["StandardFiles"]) {
            let stdString = "";
            let rawXml = standardFile["rawXml"].trim();
            if (standardFile["edited"] == true) {
                // test for empty.
                let length = rawXml.length;
                let standardJson = {};standardJson["jsonXml"] ={};;
                if(length < minLen){
                    weHaveEmpty = true;
                    //let standardJson = await this.xmlSearchService.rawXmlToJson({"rawXml" : rawXml});
                }else{
                    standardJson = await this.xmlSearchService.rawXmlToJson({"rawXml" : rawXml});
                    let loading = await this.applicationConfiguration.checkLoading(standardJson);
                    this.isLoading = loading;
                }
                if (standardJson["jsonXml"]["standards"] === undefined || length < minLen) {
                    editFailFlag = true;
                    if (standardFile["exterior"] === "0") {
                        stdString += standardFile["name"] + " / Interior";
                    } else {
                        stdString += standardFile["name"] + " / Exterior";
                    }
                } else {
                    standardFile["jsonXml"] = standardJson["jsonXml"];
                }
            }

            for (var layerFile of standardFile["layerFiles"]) {
                let layerString = "";

                if (layerFile["edited"] == true) {
                    let rawXml = layerFile["rawXml"].trim();
                    let len = rawXml.length;
                    let layerJson = {}; layerJson["jsonXml"] ={};
                    if(len < 5){
                        //{"jsonXml":{}};
                        weHaveEmpty = true;
                    }else{
                        layerJson = await this.xmlSearchService.rawXmlToJson({"rawXml" : layerFile["rawXml"]});
                        let loading = await this.applicationConfiguration.checkLoading(layerJson);
                        this.isLoading = loading;
                    }
                    //console.log(layerJson);
                    if (layerJson["jsonXml"]["layers"] === undefined) {
                        editFailFlag = true;
                        var extInt = layerFile['exterior'] == "0" ? "Interior" : "Exterior";
                        layerString += layerFile["name"] + " / " + extInt +" / " + layerFile["layer_angle"];
                    } else {
                        layerFile["jsonXml"] = layerJson["jsonXml"];
                    }
                }

                if (layerString.length > 0)
                    this.allFailEditedFiles.push(layerString);
            }

            if (stdString.length > 0)
                this.allFailEditedFiles.push(stdString);
        }

        if (editFailFlag == false) {
            //console.log("ALL FILES ARE CORRECT");
            this.submitFlag = true;

            this.finalDataForViewEdit = {"StandardFiles" : []};
            let finalDataForEditTemp = _.cloneDeep(this.getXmlFileResult["data"]);

            for (var standardFile of finalDataForEditTemp.StandardFiles) {
                let addStandardToList = false;
                if (standardFile["edited"]) {
                    addStandardToList = true;
                } else {
                    for (var layerFile of standardFile["layerFiles"]) {
                        if (layerFile["edited"]) {
                            addStandardToList = true;
                        }
                    }
                }

                if (addStandardToList == true) {
                    this.finalDataForViewEdit["StandardFiles"].push(standardFile);
                }
            }
            this.viewFailedFiles = false;
        } else {
            //console.log("SOME FILE IS INCORRECT");
            this.errorMessage = "The following files were not edited accurately:";
            if(weHaveEmpty){
                this.miniErrorMessage = " Only valid xml files can be saved. If you wish to delete a file, please use delete option";
            }else{
                this.miniErrorMessage = " only valid xml files with proper standards or layers tags can be saved ";
            }
            this.viewFailedFiles = true;
        }
    }

    async confirmEdit() {
        this.submitFlag = false;
        this.resetMessages();
        this.isLoading = true;
        let editTarget = {};
        editTarget["country"] = this.searchCriteria["selectedCountry"];
        editTarget["year"] = this.searchCriteria["selectedYear"];
        editTarget["brand"] = this.searchCriteria["selectedBrand"];
        editTarget["model"] = this.searchCriteria["selectedModel"];
        this.finalDataForViewEdit["searchCriteria"] = editTarget;
        this.editConfirmationStatus = await this.xmlEditService.editXml(this.finalDataForViewEdit);
        this.resetEditSelect();
        this.madeEdits();
        //reset the getfiles
        let loading = await this.applicationConfiguration.checkLoading(this.editConfirmationStatus);
        this.isLoading = loading;
        let getXmlFilesResult = await this.xmlSearchService.getXmlFiles();
        this.applicationConfiguration.setXmlFilesSearchResult(getXmlFilesResult);
        loading = await this.applicationConfiguration.checkLoading(getXmlFilesResult);
        this.isLoading = loading;

        //console.log(this.editConfirmationStatus);
    }

    async cancelEdit() {
        this.resetMessages();
        this.submitFlag = false;
    }

    resetMessages(){
        this.madeEdits();
        this.editConfirmationStatus = {};
        this.submitFlag = false;
        this.viewFailedFiles = false;
        this.errorMessage = "";
        this.miniErrorMessage = "";
        this.isLoading = false;
    }

    contentCatch(content: any)
    {
        return content.length > 10 ? content : "";
    }

    resetEditSelect(){
        for (var standardFile of this.getXmlFileResult["data"]["StandardFiles"]) {
            standardFile["edited"] = false;
            for (var layerFile of standardFile["layerFiles"]) {
                layerFile["edited"] = false;
            }
        }
    }

    madeEdits(){
        this.filesEdited = false;
        //console.log('made edits called');
        let getXmlFileResult = this.getXmlFileResult['data'];
        //console.log(getXmlFileResult);
        if(!this.applicationConfiguration.isEmpty(getXmlFileResult)){
            let StandardFiles = getXmlFileResult["StandardFiles"];
            if(!this.applicationConfiguration.isEmpty(StandardFiles)){
                for (var standardFile of this.getXmlFileResult["data"]["StandardFiles"]) {
                    if(standardFile["edited"] === true)
                        this.filesEdited = true;
                    for (var layerFile of standardFile["layerFiles"]) {
                        if(layerFile["edited"] === true)
                            this.filesEdited = true;
                    }
                }
            }
        }
        //console.log('file edit is '+ this.filesEdited);
    }
}