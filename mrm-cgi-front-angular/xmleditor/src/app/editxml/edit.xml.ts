import { Component, Input, Inject } from '@angular/core';
import { I18nService } from '../shared/service/i18n.service';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';
import { DOCUMENT } from '@angular/common';
import * as _ from "lodash";
import { XmlEditService } from './../shared/service/xml.edit.service';
import {XmlSearchService} from "../shared/service/xml.search.service";

@Component({
    selector: 'editxml',
    templateUrl: './edit.xml.html'
})
export class EditXml {
    public dataLoaded = false;
    public messages: any = {};
    public getXmlEditRequest : any = {};

    public standardOpenTag : any = 'standards';
    public layerOpenTag : any = 'layers';

    public selectedStandardIndex: any = -1;
    public selectedAddonIndex: any = -1;
    public selectedLayerIndex: any = -1;
    public selectedHighNameIndex: any = -1;
    public selectedSplitLayerIndex: any = -1;
    public selectedSharedLayerIndex: any = -1;

    public selectedExterior: any;
    public selectedLayerAngle: any;

    public standardModel: any;

    public isLoading: boolean;

    public finalDataForEdit: any = {};
    public submitFlag: any = false;

    public editConfirmationStatus: any = {};

    @Input() public selectedStandard: any;
    @Input() public selectedLayer: any;
    @Input() public searchCriteria: any = {};
    @Input() public layerFiles: any = {};

    @Input() public standardJsonXml: any = {};
    @Input() public layerJsonXml: any = {};

    public loading: boolean=false;
    @Input() public user: any = {username: "", password: ""};
    constructor(public i18nService: I18nService,
                @Inject(DOCUMENT) private document: Document,
                private applicationConfiguration: ApplicationConfiguration,
                private xmlSearchService: XmlSearchService,
                private xmlEditService: XmlEditService) {

        this.searchCriteria = applicationConfiguration.getSearchCriteria();
        this.messages = applicationConfiguration.getMessages();
    }

    ngOnInit() {
        if (this.dataLoaded == false) {
            this.loadRequiredData();
        }
        this.isLoading = false;
    }

    async loadRequiredData() {
        this.messages = await this.i18nService.getI18nForXmlEditor();
        this.getXmlEditRequest = this.applicationConfiguration.getXmlEditRequest();
        let editData = this.getXmlEditRequest['data'];
        if(!this.applicationConfiguration.isEmpty(editData)){
            for (var standardFile of this.getXmlEditRequest["data"]["StandardFiles"]) {
                this.selectedStandard = standardFile["name"] + ':' + standardFile["exterior"];
                this.selectedExterior = standardFile["exterior"];

                this.layerFiles = standardFile["layerFiles"];
                for (var layerFile of this.layerFiles) {
                    this.selectedLayer = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                    this.selectedLayerAngle = layerFile["layer_angle"];
                    break;
                }
                break;
            }
            this.loadFinalStdJsonXml();
            this.loadFinalLayerJsonXml();
            this.dataLoaded = true;
        }

    }

    public loadFinalStdJsonXml() {
        for (var standardFile of this.getXmlEditRequest['data']['StandardFiles']) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            this.selectedExterior = standardFile["exterior"];

            if (this.selectedStandard == checkFile) {
                this.standardModel = standardFile.model;
                this.standardJsonXml = standardFile['jsonXml'];
                break;
            }
        }
    }

    public loadFinalLayerJsonXml() {
        for (var standardFile of this.getXmlEditRequest['data']['StandardFiles']) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            if (this.selectedStandard == checkFile) {
                let layerFiles = standardFile['layerFiles'];
                for (var layerFile of layerFiles) {
                    let checkFile = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                    this.selectedLayerAngle = layerFile["layer_angle"];

                    if (this.selectedLayer == checkFile) {
                        this.layerJsonXml = layerFile['jsonXml'];
                        break;
                    }
                }
                break;
            }
        }
    }

    public shiftStandardTagUp() {
        let flag = false, index = 0;
        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            if (this.standardJsonXml["standards"][i]["selected_background"] == "selected_background") {
                flag = true;
                index = i;
            }
        }

        if (flag) {
            this.shiftUpArray(index, this.standardJsonXml["standards"]);
            this.setStandardFileEdited();
        }

        let addonIndex = 0;
        flag = false;
        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            flag = false;
            for (let j = 0; j < this.standardJsonXml["standards"][i]["standard"]["children"].length; j++) {
                if (this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] == "selected_background") {
                    flag = true;
                    addonIndex = j;
                }
            }

            if (flag) {
                this.shiftUpArray(addonIndex, this.standardJsonXml["standards"][i]["standard"]["children"]);
                this.setStandardFileEdited();
            }
        }
    }

    public shiftStandardTagDown() {
        let flag = false, index = 0;
        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            if (this.standardJsonXml["standards"][i]["selected_background"] == "selected_background") {
                flag = true;
                index = i;
            }
        }

        if (flag) {
            this.shiftDownArray(index, this.standardJsonXml["standards"]);
            this.setStandardFileEdited();
        }

        let addonIndex = 0;
        flag = false;
        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            flag = false;
            for (let j = 0; j < this.standardJsonXml["standards"][i]["standard"]["children"].length; j++) {
                if (this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] == "selected_background") {
                    flag = true;
                    addonIndex = j;
                }
            }

            if (flag) {
                this.shiftDownArray(addonIndex, this.standardJsonXml["standards"][i]["standard"]["children"]);
                this.setStandardFileEdited();
            }
        }
    }

    public deleteStandardTag() {
        let flag = false, index = 0;
        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            if (this.standardJsonXml["standards"][i]["selected_background"] == "selected_background") {
                flag = true;
                index = i;
            }
        }

        if (flag) {
            this.deleteFromArray(index, this.standardJsonXml["standards"]);
            this.setStandardFileEdited();
        }

        let addonIndex = 0;
        flag = false;
        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            flag = false;
            for (let j = 0; j < this.standardJsonXml["standards"][i]["standard"]["children"].length; j++) {
                if (this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] == "selected_background") {
                    flag = true;
                    addonIndex = j;
                }
            }

            if (flag) {
                this.deleteFromArray(addonIndex, this.standardJsonXml["standards"][i]["standard"]["children"]);
                this.setStandardFileEdited();
            }
        }
    }

    public cloneStandardTag() {
        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            if (this.standardJsonXml["standards"][i]["selected_background"] != "cloned_background") {
                this.standardJsonXml["standards"][i]["selected_background"] = "";
            }
            for (let j = 0; j < this.standardJsonXml["standards"][i]["standard"]["children"].length; j++) {
                if (this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] != "cloned_background") {
                    this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] = "";
                }
            }
        }

        if (this.selectedStandardIndex != -1 && this.selectedAddonIndex != -1) {
            this.cloneArray(this.selectedAddonIndex, this.standardJsonXml["standards"][this.selectedStandardIndex]["standard"]["children"]);
            this.setStandardFileEdited();

            this.standardJsonXml["standards"][this.selectedStandardIndex]["standard"]["children"][this.selectedAddonIndex+1]["selected_background"] = "cloned_background";
        } else if (this.selectedStandardIndex != -1) {
            this.cloneArray(this.selectedStandardIndex, this.standardJsonXml["standards"]);
            this.setStandardFileEdited();

            this.standardJsonXml["standards"][this.selectedStandardIndex+1]["selected_background"] = "cloned_background";
            for (let j = 0; j < this.standardJsonXml["standards"][this.selectedStandardIndex+1]["standard"]["children"].length; j++) {
                this.standardJsonXml["standards"][this.selectedStandardIndex+1]["standard"]["children"][j]["selected_background"] = "";
            }
        }

        this.selectedStandardIndex = -1;
        this.selectedAddonIndex = -1;
    }

    public selectStandardTag(id) {
        this.selectedStandardIndex = parseInt(id);
        this.selectedAddonIndex = -1;

        let standardTag = this.standardJsonXml["standards"][parseInt(id)];

        if (standardTag["selected_background"] == "selected_background") {
            this.selectedStandardIndex = -1;
            standardTag["selected_background"] = "";
        } else {
            standardTag["selected_background"] = "selected_background";
            for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
                if (i != parseInt(id)) {
                    if (this.standardJsonXml["standards"][i]["selected_background"] != "cloned_background") {
                        this.standardJsonXml["standards"][i]["selected_background"] = "";
                    }
                }
            }

            for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
                for (let j = 0; j < this.standardJsonXml["standards"][i]["standard"]["children"].length; j++) {
                    if (this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] != "cloned_background") {
                        this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] = "";
                        this.selectedAddonIndex = -1;
                    }
                }
            }
        }
    }

    public selectAddonTag(id) {
        let split = id.split('::');

        this.selectedStandardIndex = parseInt(split[0]);
        this.selectedAddonIndex = parseInt(split[1]);

        for (let i = 0; i < this.standardJsonXml["standards"].length; i++) {
            this.standardJsonXml["standards"][i]["selected_background"] = "";

            for (let j = 0; j < this.standardJsonXml["standards"][i]["standard"]["children"].length; j++) {
                if (i == parseInt(split[0]) && j == parseInt(split[1])) {
                    let background = this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"];

                    if (background != "selected_background") {
                        this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] = "selected_background";
                    } else {
                        if (this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] != "cloned_background") {
                            this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] = "";
                        }
                    }
                } else {
                    if (this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] != "cloned_background") {
                        this.standardJsonXml["standards"][i]["standard"]["children"][j]["selected_background"] = "";
                    }
                }
            }
        }
    }

    public shiftLayerTagUp() {
        let flag = false, index = 0;
        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            if (this.layerJsonXml["layers"][i]["selected_background"] == "selected_background") {
                flag = true;
                index = i;
            }
        }

        if (flag) {
            this.shiftUpArray(index, this.layerJsonXml["layers"]);
            this.setLayerFileEdited();
        }

        let splitOrSharedLayerIndex = 0;
        flag = false;
        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            flag = false;
            for (let j = 0; j < this.layerJsonXml["layers"][i]["layer"]["children"].length; j++) {
                if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] == "selected_background") {
                    flag = true;
                    splitOrSharedLayerIndex = j;
                }
            }

            if (flag) {
                this.shiftUpArray(splitOrSharedLayerIndex, this.layerJsonXml["layers"][i]["layer"]["children"]);
                this.setLayerFileEdited();
            }
        }
    }

    public shiftLayerTagDown() {
        let flag = false, index = 0;
        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            if (this.layerJsonXml["layers"][i]["selected_background"] == "selected_background") {
                flag = true;
                index = i;
            }
        }

        if (flag) {
            this.shiftDownArray(index, this.layerJsonXml["layers"]);
            this.setLayerFileEdited();
        }

        let splitOrSharedLayerIndex = 0;
        flag = false;
        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            flag = false;
            for (let j = 0; j < this.layerJsonXml["layers"][i]["layer"]["children"].length; j++) {
                if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] == "selected_background") {
                    flag = true;
                    splitOrSharedLayerIndex = j;
                }
            }

            if (flag) {
                this.shiftDownArray(splitOrSharedLayerIndex, this.layerJsonXml["layers"][i]["layer"]["children"]);
                this.setLayerFileEdited();
            }
        }
    }

    public deleteLayerTag() {
        let flag = false, index = 0;
        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            if (this.layerJsonXml["layers"][i]["selected_background"] == "selected_background") {
                flag = true;
                index = i;
            }
        }

        if (flag) {
            this.deleteFromArray(index, this.layerJsonXml["layers"]);
            this.setLayerFileEdited();
        }

        let splitOrSharedLayerIndex = 0;
        flag = false;
        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            flag = false;
            for (let j = 0; j < this.layerJsonXml["layers"][i]["layer"]["children"].length; j++) {
                if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] == "selected_background") {
                    flag = true;
                    splitOrSharedLayerIndex = j;
                }
            }

            if (flag) {
                this.deleteFromArray(splitOrSharedLayerIndex, this.layerJsonXml["layers"][i]["layer"]["children"]);
                this.setLayerFileEdited();
            }
        }
    }

    public cloneLayerTag() {
        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            if (this.layerJsonXml["layers"][i]["selected_background"] != "cloned_background") {
                this.layerJsonXml["layers"][i]["selected_background"] = "";
            }
            for (let j = 0; j < this.layerJsonXml["layers"][i]["layer"]["children"].length; j++) {
                if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "cloned_background") {
                    this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "";
                }
            }
        }

        if (this.selectedLayerIndex != -1) {
            if (this.selectedSplitLayerIndex != -1) {
                this.cloneArray(this.selectedSplitLayerIndex, this.layerJsonXml["layers"][this.selectedLayerIndex]["layer"]["children"]);
                this.setLayerFileEdited();

                this.layerJsonXml["layers"][this.selectedLayerIndex]["layer"]["children"][this.selectedSplitLayerIndex+1]["selected_background"] = "cloned_background";
            } else if (this.selectedSharedLayerIndex != -1) {
                this.cloneArray(this.selectedSharedLayerIndex, this.layerJsonXml["layers"][this.selectedLayerIndex]["layer"]["children"]);
                this.setLayerFileEdited();

                this.layerJsonXml["layers"][this.selectedLayerIndex]["layer"]["children"][this.selectedSharedLayerIndex+1]["selected_background"] = "cloned_background";
            } else {
                this.cloneArray(this.selectedLayerIndex, this.layerJsonXml["layers"]);
                this.setLayerFileEdited();

                this.layerJsonXml["layers"][this.selectedLayerIndex+1]["selected_background"] = "cloned_background";
                for (let j = 0; j < this.layerJsonXml["layers"][this.selectedLayerIndex+1]["layer"]["children"].length; j++) {
                    this.layerJsonXml["layers"][this.selectedLayerIndex+1]["layer"]["children"][j]["selected_background"] = "";
                }
            }
        }

        this.selectedLayerIndex = -1;
        this.selectedSplitLayerIndex = -1;
        this.selectedSharedLayerIndex = -1;
    }

    public selectLayerTag(id) {
        this.selectedLayerIndex = parseInt(id);
        this.selectedSplitLayerIndex = -1;
        this.selectedSharedLayerIndex = -1;

        let layerTag = this.layerJsonXml["layers"][parseInt(id)];

        if (layerTag["selected_background"] == "selected_background") {
            this.selectedStandardIndex = -1;

            if (layerTag["selected_background"] != "cloned_background") {
                layerTag["selected_background"] = "";
            }
        } else {
            layerTag["selected_background"] = "selected_background";
            for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
                if (i != parseInt(id)) {
                    if (this.layerJsonXml["layers"][i]["selected_background"] != "cloned_background") {
                        this.layerJsonXml["layers"][i]["selected_background"] = "";
                    }
                }
            }

            for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
                for (let j = 0; j < this.layerJsonXml["layers"][i]["layer"]["children"].length; j++) {
                    if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "cloned_background") {
                        this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "";
                        this.selectedHighNameIndex = -1;
                    }
                }
            }
        }
    }

    public selectSharedLayerTag(id) {
        let split = id.split('::');

        this.selectedLayerIndex = parseInt(split[0]);
        this.selectedSharedLayerIndex = parseInt(split[1]);

        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            if (this.layerJsonXml["layers"][i]["selected_background"] != "cloned_background") {
                this.layerJsonXml["layers"][i]["selected_background"] = "";
            }

            for (let j = 0; j < this.layerJsonXml["layers"][i]["layer"]["children"].length; j++) {
                if (i == parseInt(split[0]) && j == parseInt(split[1])) {
                    if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "selected_background") {
                        this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "selected_background";
                    } else {
                        if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "cloned_background") {
                            this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "";
                        }
                    }
                } else {
                    if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "cloned_background") {
                        this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "";
                    }
                }
            }
        }
    }

    public selectSplitLayerTag(id) {
        let split = id.split('::');

        this.selectedLayerIndex = parseInt(split[0]);
        this.selectedSplitLayerIndex = parseInt(split[1]);

        for (let i = 0; i < this.layerJsonXml["layers"].length; i++) {
            if (this.layerJsonXml["layers"][i]["selected_background"] != "cloned_background") {
                this.layerJsonXml["layers"][i]["selected_background"] = "";
            }

            for (let j = 0; j < this.layerJsonXml["layers"][i]["layer"]["children"].length; j++) {
                if (i == parseInt(split[0]) && j == parseInt(split[1])) {
                    if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "selected_background") {
                        this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "selected_background";
                    } else {
                        if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "cloned_background") {
                            this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "";
                        }
                    }
                } else {
                    if (this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] != "cloned_background") {
                        this.layerJsonXml["layers"][i]["layer"]["children"][j]["selected_background"] = "";
                    }
                }
            }
        }
    }

    async resetXml() {
        this.resetMessages();
        this.getXmlEditRequest = this.applicationConfiguration.getResetXmlEditRequest();

        for (var standardFile of this.getXmlEditRequest["data"]["StandardFiles"]) {
            this.selectedStandard = standardFile["name"] + ':' + standardFile["exterior"];
            this.selectedExterior = standardFile["exterior"];
            this.layerFiles = standardFile["layerFiles"];
            for (var layerFile of this.layerFiles) {
                this.selectedLayer = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                this.selectedLayerAngle = layerFile["layer_angle"];
                break;
            }
            break;
        }

        for (let standardFile of this.getXmlEditRequest["data"]["StandardFiles"]) {
            standardFile["edited"] = false;
            for (let layerFile of standardFile["layerFiles"]) {
                layerFile["edited"] = false;
            }
        }

        this.loadFinalStdJsonXml();
        this.loadFinalLayerJsonXml();
    }

    public changeLayerXml() {
        for (var standardFile of this.getXmlEditRequest['data']['StandardFiles']) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            if (this.selectedStandard == checkFile) {
                this.standardModel = standardFile.model;
                this.layerFiles = standardFile['layerFiles'];
                for (var layerFile of this.layerFiles) {
                    this.selectedLayer = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                    this.selectedLayerAngle = layerFile["layer_angle"];
                    this.layerJsonXml = layerFile['jsonXml'];
                    break;
                }
                break;
            }
        }
    }

    public editXml() {
    }

    async submitXml() {
        this.resetMessages();
        //this.isLoading = true;
        this.submitFlag = true;
        this.finalDataForEdit = {"StandardFiles" : []};
        //console.log(this.getXmlEditRequest);
        //let finalDataForEditTemp = _.cloneDeep(this.getXmlEditRequest['data']);

        // for (var standardFile of finalDataForEditTemp.StandardFiles) {
        for(var standardFile of this.getXmlEditRequest['data']['StandardFiles']){
            //console.log(standardFile);
            let addStandardToList = false;
            if (standardFile["edited"]) {
                var stdRawJson = standardFile["jsonXml"];
                var stdRawXml = await this.xmlSearchService.getRawXml({"jsonXml" : stdRawJson});
                //console.log(stdRawXml);
                standardFile['rawXml'] = stdRawXml['rawXml'];
                addStandardToList = true;
                for (var layerFile of standardFile["layerFiles"]) {
                    if (layerFile["edited"]) {
                        var layRawJson = layerFile["jsonXml"];
                        var layRawXml = await this.xmlSearchService.getRawXml({"jsonXml" : layRawJson});
                        layerFile['rawXml'] = layRawXml['rawXml'];
                    }
                }
            } else {
                for (var layerFile of standardFile["layerFiles"]) {
                    if (layerFile["edited"]) {
                        addStandardToList = true;
                        var layRawJson = layerFile["jsonXml"];
                        var layRawXml = await this.xmlSearchService.getRawXml({"jsonXml" : layRawJson});
                        layerFile['rawXml'] = layRawXml['rawXml'];
                    }
                }
            }

            if (addStandardToList == true) {
                this.finalDataForEdit["StandardFiles"].push(standardFile);
            }
        }
    }

    async confirmEdit() {
        this.resetMessages();
        this.isLoading = true;
        this.submitFlag = false;
        let editTarget = {};
        editTarget["country"] = this.searchCriteria["selectedCountry"];
        editTarget["year"] = this.searchCriteria["selectedYear"];
        editTarget["brand"] = this.searchCriteria["selectedBrand"];
        editTarget["model"] = this.searchCriteria["selectedModel"];
        this.finalDataForEdit["searchCriteria"] = editTarget;
        this.editConfirmationStatus = await this.xmlEditService.editXml(this.finalDataForEdit);
        let loading = await this.applicationConfiguration.checkLoading(this.editConfirmationStatus);
        this.isLoading = loading;
        let getXmlFilesResult = await this.xmlSearchService.getXmlFiles();
        this.applicationConfiguration.setXmlFilesSearchResult(getXmlFilesResult);
        loading = await this.applicationConfiguration.checkLoading(getXmlFilesResult);
        this.isLoading = loading;
        this.resetXmlEdit();
    }

    public removePleaseSelect(item){
        if(item=='pleaseSelect'){
            return '';
        }
        return item;
    }

    async cancelEdit() {
        this.submitFlag = false;
    }

    public setStandardFileEdited() {
        for (var standardFile of this.getXmlEditRequest['data']['StandardFiles']) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            if (this.selectedStandard == checkFile) {
                standardFile["edited"] = true;
                break;
            }
        }
    }

    public setLayerFileEdited() {
        for (var standardFile of this.getXmlEditRequest['data']['StandardFiles']) {
            let checkFile = standardFile["name"] + ':' + standardFile["exterior"];
            if (this.selectedStandard == checkFile) {
                for (var layerFile of this.layerFiles) {
                    let checkLayerFile = layerFile["name"] + ':' + layerFile["exterior"] + ':' + layerFile["layer_angle"];
                    if (this.selectedLayer == checkLayerFile) {
                        layerFile["edited"] = true;
                        break;
                    }
                }
                break;
            }
        }
    }

    private shiftUpArray(indexToMove, arr) {
        for (let i = 0; i < arr.length; i++) {
            if (i == indexToMove) {
                if (i - 1 >= 0) {
                    let temp = arr[i];
                    arr[i] = arr[i-1];
                    arr[i-1] = temp;
                }
            }
        }
    }

    private shiftDownArray(indexToMove, arr) {
        for (let i = 0; i < arr.length; i++) {
            if (i == indexToMove) {
                if (i + 1 <= arr.length - 1) {
                    let temp = arr[i];
                    arr[i] = arr[i+1];
                    arr[i+1] = temp;
                }
            }
        }
    }

    private deleteFromArray(indexToDelete, arr) {
        arr.splice(indexToDelete, 1);
    }

    private cloneArray(indexToClone, arr) {
        let clone = _.cloneDeep(arr[indexToClone]);
        arr.splice(indexToClone + 1, 0, clone);
    }

    public resetMessages(){
        this.editConfirmationStatus = {};
        this.isLoading=false;
    }

    public resetXmlEdit(){
        for (let standardFile of this.getXmlEditRequest["data"]["StandardFiles"]) {
            standardFile["edited"] = false;
            for (let layerFile of standardFile["layerFiles"]) {
                layerFile["edited"] = false;
            }
        }
    }
}