import { Component, Input, Inject } from '@angular/core';
import { I18nService } from '../shared/service/i18n.service';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';
import { DOCUMENT } from '@angular/common';
import { XmlCloneService } from '../shared/service/xml.clone.service';
import { XmlSearchService } from '../shared/service/xml.search.service';
import * as _ from "lodash";

@Component({
    selector: 'clonexml',
    templateUrl: './clone.xml.html'
})
export class CloneXml {
    public dataLoaded = false;
    public messages: any = {};

    public searchCriteria: any;
    public cloneTargetSearchCriteria: any;

    public submitFlag: any = false;
    public validateFlag: any = false;

    public cloneConfirmationStatus: any = {};

    public newModelTextField: any = "";

    public errorMessage: any = "";

    public canSubmit = false;

    public isLoading: boolean;

    public successMessage: any = "";

    public finalDataForClone: any = {};
    @Input() public xmlCloneRequest: any = {};

    public loading: boolean=false;
    @Input() public user: any = {username: "", password: ""};
    constructor(public i18nService: I18nService,
                private xmlCloneService: XmlCloneService,
                private xmlSearchService: XmlSearchService,
                @Inject(DOCUMENT) private document: Document,
                private applicationConfiguration: ApplicationConfiguration) {
        this.searchCriteria = applicationConfiguration.getSearchCriteria();
        this.xmlCloneRequest = applicationConfiguration.getXmlCloneRequest();

    }

    ngOnInit() {
        if (this.dataLoaded == false) {
            this.loadRequiredData();
        }
        if(!this.applicationConfiguration.isEmpty(this.xmlCloneRequest)){
            this.canSubmit = true;
        }
        this.isLoading = false;
    }

    async loadRequiredData() {
        this.messages = await this.i18nService.getI18nForXmlEditor();
        this.dataLoaded = true;
    }

    async resetXml() {
        this.resetMessages();
        this.xmlCloneRequest = this.applicationConfiguration.getResetXmlCloneRequest();
        this.submitFlag = false;
    }

    async submitXml() {
        this.resetMessages();
        this.submitFlag = true;
        this.isLoading = true;
        let cloneFileSelected = false;
        this.finalDataForClone = {"StandardFiles" : []};
        let finalDataForCloneTemp = _.cloneDeep(this.xmlCloneRequest);

        for (var standardFile of finalDataForCloneTemp.StandardFiles) {
            let addStandardToList = false;
            if (standardFile["cloned"]) {
                standardFile["clone"] = true;
                addStandardToList = true;
                for (var layerFile of standardFile["layerFiles"]) {
                    if (layerFile["cloned"]) {
                        layerFile["clone"] = true;
                        layerFile["cloned"] = true;
                        layerFile["originalName"] = layerFile["name"];
                    }
                }
            } else {
                for (var layerFile of standardFile["layerFiles"]) {
                    if (layerFile["cloned"]) {
                        layerFile["clone"] = true;
                        addStandardToList = true;
                        layerFile["originalName"] = layerFile["name"];
                    } else {
                        layerFile["clone"] = false;
                    }
                }
            }

            if (addStandardToList == true) {
                standardFile["originalName"] = standardFile["name"];
                cloneFileSelected = true;
                this.finalDataForClone["StandardFiles"].push(standardFile);
            }

        }
        if(cloneFileSelected == false){
            this.errorMessage = "Please select files and click appropriate clone option to submit. ";
            this.submitFlag = false;
        }

        this.cloneTargetSearchCriteria = this.applicationConfiguration.getResetCloneTargetSearchCriteria();
        this.cloneTargetSearchCriteria['countries'] = await this.xmlSearchService.getCountries();
        let loading = await this.applicationConfiguration.checkLoading(this.cloneTargetSearchCriteria['countries']);
        this.isLoading = loading;

        this.validateAllFiles();
    }

    async confirmClone() {
        this.submitFlag = false;
        this.resetMessages();
        this.isLoading = true;
        let cloneTarget = {};
        cloneTarget["country"] = this.cloneTargetSearchCriteria["selectedCountry"];
        cloneTarget["year"] = this.cloneTargetSearchCriteria["selectedYear"];
        cloneTarget["brand"] = this.cloneTargetSearchCriteria["selectedBrand"];
        if (this.newModelTextField.trim().length <= 0) {
            cloneTarget["model"] = this.cloneTargetSearchCriteria["selectedModel"];
        } else {
            cloneTarget["model"] = this.newModelTextField;
        }

        this.finalDataForClone["targetCriteria"] = cloneTarget;

        let cloneSearchCriteria = {};
        cloneSearchCriteria["country"] = this.searchCriteria["selectedCountry"];
        cloneSearchCriteria["year"] = this.searchCriteria["selectedYear"];
        cloneSearchCriteria["brand"] = this.searchCriteria["selectedBrand"];
        cloneSearchCriteria["model"] = this.searchCriteria["selectedModel"];
        this.finalDataForClone["searchCriteria"] = cloneSearchCriteria;

        this.cloneConfirmationStatus = await this.xmlCloneService.cloneXml(this.finalDataForClone);
        let loading = await this.applicationConfiguration.checkLoading(this.cloneConfirmationStatus);
        this.isLoading = loading;
    }

    public validateAllFiles() {
        //this.resetMessages();
        for(let standardFile of this.finalDataForClone["StandardFiles"]) {
            if (!this.validateFileName(standardFile["name"], "standards")) {
                this.validateFlag = false;
                return;
            }

            for (var layerFile of standardFile["layerFiles"]) {
                if (layerFile["cloned"]) {
                    if (!this.validateFileName(layerFile["name"], "layers")) {
                        this.validateFlag = false;
                        return;
                    }
                }
            }
        }

        if (this.cloneTargetSearchCriteria["selectedCountry"] == "pleaseSelect") {
            this.validateFlag = false;
            return;
        }

        if (this.cloneTargetSearchCriteria["selectedYear"] == "pleaseSelect") {
            this.validateFlag = false;
            return;
        }

        if (this.cloneTargetSearchCriteria["selectedBrand"] == "pleaseSelect") {
            this.validateFlag = false;
            return;
        }

        if (this.newModelTextField.trim().length <= 0 && this.cloneTargetSearchCriteria["selectedModel"] == "pleaseSelect") {
            this.validateFlag = false;
            return;
        }

        this.validateFlag = true;
    }

    private validateFileName(name, stdOrLayer) {
        let split = name.split("_");
        let underscores = split.length - 1;

        if (underscores < 3)
            return false;

        if (stdOrLayer == "standards") {
            if (split[0] != "standards")
                return false;
        }

        if (stdOrLayer == "layers") {
            if (split[0] != "layers")
                return false;
        }

        if (split[1] != this.cloneTargetSearchCriteria["selectedYear"])
            return false;

        if (name.substr(name.length - 4) != ".xml")
            return false;

        let country = split[underscores].split(".")[0];
        if (country != this.cloneTargetSearchCriteria["selectedCountry"])
            return false;

        return true;
    }

    async selectCountry() {
        this.resetMessages();
        this.applicationConfiguration.resetCloneSearchCriteriaYears();
        this.applicationConfiguration.resetCloneSearchCriteriaBrands();
        this.applicationConfiguration.resetCloneSearchCriteriaModels();

        //this.cloneTargetSearchCriteria['years'] = await this.xmlSearchService.getYears();
        this.cloneTargetSearchCriteria['years'] = await this.xmlSearchService.getAllYears();
        this.validateAllFiles();
    }

    async selectYear() {
        this.resetMessages();
        this.applicationConfiguration.resetCloneSearchCriteriaBrands();
        this.applicationConfiguration.resetCloneSearchCriteriaModels();

        if (this.searchCriteria['year'] == 'pleaseSelect') return;
        //this.cloneTargetSearchCriteria['brands'] = await this.xmlSearchService.getBrands();
        this.cloneTargetSearchCriteria['brands'] = await this.xmlSearchService.getAllBrands();
        this.validateAllFiles();
    }

    async selectBrand() {
        this.resetMessages();
        this.applicationConfiguration.resetCloneSearchCriteriaModels();

        if (this.cloneTargetSearchCriteria['brands'] == 'pleaseSelect') return;
        //this.cloneTargetSearchCriteria['models'] = await this.xmlSearchService.getModels();
        this.cloneTargetSearchCriteria['models'] = await this.xmlSearchService.getAllModels();
        this.validateAllFiles();
    }

    async selectModel() {
        this.resetMessages();
        this.validateAllFiles();
    }

    public cancelClone() {
        this.resetMessages();
        this.submitFlag = false;
    }

    public removePleaseSelect(item){
        if(item=='pleaseSelect'){
            return '';
        }
        return item;
    }

    async deleteStandards() {
        this.resetMessages();
        let temp = _.cloneDeep(this.xmlCloneRequest);

        let stdFiles = [];
        for (var standardFile of temp["StandardFiles"]) {
            if (standardFile["clone"] && standardFile["cloned"]) {
                //do nothing
            } else {
                stdFiles.push(standardFile);
            }
        }

        temp["StandardFiles"] = stdFiles;
        this.xmlCloneRequest = {"StandardFiles" : []};
        for (var standardFile of temp["StandardFiles"]) {
            this.xmlCloneRequest["StandardFiles"].push(standardFile);
        }
    }

    async deleteLayers() {
        this.resetMessages();
        let temp = _.cloneDeep(this.xmlCloneRequest);

        let stdFiles = [];
        for (var standardFile of temp["StandardFiles"]) {
            let layerFiles = standardFile["layerFiles"];
            standardFile["layerFiles"] = [];
            for (var layerFile of layerFiles) {
                if (layerFile["clone"] && layerFile["cloned"]) {
                    //do nothing
                } else {
                    standardFile["layerFiles"].push(layerFile);
                }
            }
            stdFiles.push(standardFile);
        }

        temp["StandardFiles"] = stdFiles;
        this.xmlCloneRequest = {"StandardFiles" : []};
        for (var standardFile of temp["StandardFiles"]) {
            this.xmlCloneRequest["StandardFiles"].push(standardFile);
        }
    }

    async cloneStandards() {
        let cloneStandard = {"StandardFiles" : []};

        for (var standardFile of this.xmlCloneRequest["StandardFiles"]) {
            let stdFile = _.cloneDeep(standardFile);
            cloneStandard["StandardFiles"].push(stdFile);
            if (standardFile["clone"]) {
                let clonedStdFile = _.cloneDeep(standardFile);
                clonedStdFile["cloned"] = true;

                let layerFiles = clonedStdFile["layerFiles"];
                clonedStdFile["layerFiles"] = [];
                for (var layerFile of layerFiles) {
                    if (layerFile["clone"]) {
                        let cloneLayerFile = _.cloneDeep(layerFile);
                        cloneLayerFile["cloned"] = true;
                        clonedStdFile["layerFiles"].push(cloneLayerFile);
                    }
                }

                cloneStandard["StandardFiles"].push(clonedStdFile);
            }
        }

        this.xmlCloneRequest = {"StandardFiles" : []};
        for (var standardFile of cloneStandard["StandardFiles"]) {
            this.xmlCloneRequest["StandardFiles"].push(standardFile);
        }
    }

    async cloneLayers() {
        this.resetMessages();
        let cloneLayer = _.cloneDeep(this.xmlCloneRequest);

        for (var standardFile of cloneLayer["StandardFiles"]) {
            let layerFiles = standardFile["layerFiles"];
            standardFile["layerFiles"] = [];
            for (var layerFile of layerFiles) {
                let currentLayer = _.cloneDeep(layerFile);
                standardFile["layerFiles"].push(currentLayer);

                if (layerFile["clone"]) {
                    let layerClone = _.cloneDeep(layerFile);
                    layerClone["cloned"] = true;
                    standardFile["layerFiles"].push(layerClone);
                }
            }
        }

        this.xmlCloneRequest = {"StandardFiles" : []};
        for (var standardFile of cloneLayer["StandardFiles"]) {
            this.xmlCloneRequest["StandardFiles"].push(standardFile);
        }
    }

    public selectAllLayers() {
        this.resetMessages();
        for (var standardFile of this.xmlCloneRequest["StandardFiles"]) {
            let layerFiles = standardFile["layerFiles"];
            for (var layerFile of layerFiles) {
                layerFile["clone"] = standardFile["clone"];
            }
        }
    }

    public resetMessages()
    {
        this.errorMessage ="";
        this.successMessage="";
        this.cloneConfirmationStatus={};
    }
}