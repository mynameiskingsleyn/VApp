import { ApplicationConfiguration } from './../shared/applicationconfiguration/application.configuration';
import { Component, Input, Inject } from '@angular/core';
import { I18nService } from '../shared/service/i18n.service';
import { XmlDeleteService } from '../shared/service/xml.delete.service';
import { XmlSearchService } from '../shared/service/xml.search.service';
import { DOCUMENT } from '@angular/common';
import * as _ from "lodash";

@Component({
    selector: 'deletexml',
    templateUrl: './delete.xml.html'
})
export class DeleteXml {
    public dataLoaded = false;
    public performDeleteStandard = false;
    public performDeleteLayer = false;
    public messages: any = {};

    public searchCriteria: any;

    public canConfirm: any = false;

    public checkSelectAllXmls: boolean = false;
    public checkSelectAllLayerXmls: boolean = false;
    public submitFlag: any = false;
    public errorMessage: any = "";

    public canSubmit: any = false;

    public deleteConfirmationStatus: any = {};

    @Input() public xmlDeleteRequest: any = {"data" : {}};
    @Input() public finalDataForDelete: any = {};

    public loading: boolean=false;
    @Input() public user: any = {username: "", password: ""};
    constructor(public i18nService: I18nService,
                private xmlDeleteService: XmlDeleteService,
                private xmlSearchService: XmlSearchService,
                @Inject(DOCUMENT) private document: Document,
                private applicationConfiguration: ApplicationConfiguration) {
        this.searchCriteria = applicationConfiguration.getSearchCriteria();
        this.loadRequiredData();
    }

    async loadRequiredData() {
        this.messages = await this.i18nService.getI18nForXmlEditor();
        this.xmlDeleteRequest = this.applicationConfiguration.getXmlDeleteRequest();
        let deleteRequest = this.xmlDeleteRequest['data'];
        if(!this.applicationConfiguration.isEmpty(deleteRequest)){
            this.canSubmit = true;
        }
        this.dataLoaded = true;
    }

    async resetXml() {
        this.checkSelectAllXmls = false;
        this.checkSelectAllLayerXmls = false;
        this.xmlDeleteRequest = this.applicationConfiguration.getResetXmlDeleteRequest();
        this.submitFlag = false;
    }

    async submitXml() {
        this.errorMessage = "Please select file(s) to delete";
        this.submitFlag = true;
        this.finalDataForDelete = {"StandardFiles" : []};
        let finalDataForDeleteTemp = _.cloneDeep(this.xmlDeleteRequest["data"]);
        this.canConfirm = false;
        for (var standardFile of finalDataForDeleteTemp.StandardFiles) {
            let addStandardToList = false;
            if (standardFile["delete"]) {
                addStandardToList = true;
                for (var layerFile of standardFile["layerFiles"]) {
                    layerFile["delete"] = true;
                }
            } else {
                for (var layerFile of standardFile["layerFiles"]) {
                    if (layerFile["delete"]) {
                        addStandardToList = true;
                    }
                }
            }

            if (addStandardToList == true) {
                this.canConfirm = true;
                this.errorMessage ="";
                this.finalDataForDelete["StandardFiles"].push(standardFile);
            }
        }
    }

    async confirmDelete() {
        this.submitFlag = false;
        this.deleteConfirmationStatus = await this.xmlDeleteService.deleteXml(this.finalDataForDelete);
        let xmlSearchResult = await this.xmlSearchService.searchXml();
        this.applicationConfiguration.setXmlSearchResult(xmlSearchResult);
        this.applicationConfiguration.getXmlDeleteRequest();
        this.applicationConfiguration.getResetXmlCloneRequest();
        this.finalDataForDelete = {"StandardFiles" : []};

        this.resetXml();
    }

    async cancelDelete() {
        this.submitFlag = false;
    }

    public removePleaseSelect(item){
        if(item=='pleaseSelect'){
            return '';
        }
        return item;
    }

    async deleteStandards() {
        this.performDeleteStandard = true;
        this.performDeleteLayer = true;
        let resetMethod = this.resetPerformDeleteFlags;
        setTimeout(resetMethod, 1000);
    }

    resetPerformDeleteFlags() {
        this.performDeleteStandard = false;
        this.performDeleteLayer = false;
    }

    async deleteLayers() {
        this.performDeleteLayer = true;
        let resetMethod = this.resetPerformDeleteFlags;
        setTimeout(resetMethod, 1000);
    }
}