import { Injectable } from '@angular/core';
import { CookieService } from 'ngx-cookie-service';
import * as _ from "lodash";

@Injectable()
export class ApplicationConfiguration {
    private baseAppUrl: string = "";
    private baseDataUrl: string = "";

    private searchCriteria = {
        "selectedYear":  "pleaseSelect",
        "selectedCountry": "pleaseSelect",
        "selectedBrand": "pleaseSelect",
        "selectedModel": "pleaseSelect",
		"selectedMmc": "pleaseSelect",
        "dataLoaded": false,
        "searchResultLoaded": false,
        "getXmlFilesLoaded": false,
        "years": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }],
        "countries": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }],
        "brands": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }],
        "models": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }],
		"mmc": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }]
    };

    private cloneTargetSearchCriteria = {
        "selectedYear":  "pleaseSelect",
        "selectedCountry": "pleaseSelect",
        "selectedBrand": "pleaseSelect",
        "selectedModel": "pleaseSelect",
        "dataLoaded": false,
        "searchResultLoaded": false,
        "getXmlFilesLoaded": false,
        "years": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }],
        "countries": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }],
        "brands": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }],
        "models": [{
            "name" : "pleaseSelect",
            "value" : "Please Select"
        }]
    };

    private xmlSearchResult: any = {"data" : {}};

    private xmlFilesSearchResult: any = {"data" : {}};

    private xmlDeleteRequest: any = {"data": {}};

    private xmlDeleteRequestCompleted : boolean = false;

    private xmlCloneRequestCompleted : boolean = false;

    private xmlCloneRequest: any = {};

    private xmlEditRequest : any;

    private xmlEditRequestCompleted : boolean = false;

    private messages: any = {};
    
	constructor(private _cookieService:CookieService) {
    }
    
    public setBaseDataUrl(baseDataUrl:string) {
        this.baseDataUrl = baseDataUrl;
    }
    
    public setBaseApplicationUrl(baseAppUrl:string) {
        this.baseAppUrl = baseAppUrl;
    }
    
    public getBaseDataUrl() {
        return this.baseDataUrl;
    }
    
    public getBaseApplicationUrl() {
        return this.baseAppUrl;
    }

    public getSearchCriteria() : any {
        return this.searchCriteria;
    }

    public getXmlSearchResult() : any {
        return this.xmlSearchResult;
    }

    public setXmlSearchResult(xmlSearchResult: any) : any {
        this.xmlSearchResult["data"] = xmlSearchResult;
    }

    public getXmlFilesSearchResult() : any {
        return this.xmlFilesSearchResult;
    }

    public setXmlFilesSearchResult(xmlFilesSearchResult: any) : any {
        this.xmlFilesSearchResult["data"] = xmlFilesSearchResult;
    }

    public getMessages() : any {
        return this.messages;
    }

    public setMessages(messages: any) : any {
        this.messages = messages;
    }

    public getCloneTargetSearchCriteria() {
        return this.cloneTargetSearchCriteria;
    }

    public getXmlEditRequest() : any {
        if (this.xmlEditRequestCompleted == false) {
            this.getResetXmlEditRequest();
        }
        return this.xmlEditRequest;
    }

    public getResetXmlEditRequest() : any {
        this.xmlEditRequest = {};
        this.xmlEditRequest["data"] = _.cloneDeep(this.xmlFilesSearchResult["data"]);
        this.xmlEditRequestCompleted = true;
        return this.xmlEditRequest;
    }

    public getXmlDeleteRequest() : any {
        if (this.xmlDeleteRequestCompleted == false) {
            this.getResetXmlDeleteRequest();
        }
        return this.xmlDeleteRequest;
    }

    public getResetXmlDeleteRequest() : any {
        this.xmlDeleteRequest["data"] = _.cloneDeep(this.xmlSearchResult["data"]);
        this.xmlDeleteRequestCompleted = true;
        return this.xmlDeleteRequest;
    }

    public getXmlCloneRequest() : any {
        if (this.xmlCloneRequestCompleted == false) {
            this.getResetXmlCloneRequest();
        }
        return this.xmlCloneRequest;
    }

    public getResetXmlCloneRequest() : any {
        this.xmlCloneRequest = _.cloneDeep(this.xmlSearchResult["data"]);
        this.xmlCloneRequestCompleted = true;
        return this.xmlCloneRequest;
    }

    public getResetCloneTargetSearchCriteria() {
        return this.cloneTargetSearchCriteria;
    }
    
    public getCookieString(cookieName: string) {
        return this._cookieService.get(cookieName);
    }

    public resetSearchCriteriaCountries() {
        this.searchCriteria["selectedCountry"] = "pleaseSelect";
        this.searchCriteria["countries"]["name"] = "pleaseSelect";
        this.searchCriteria["countries"]["value"] = "Please Select Country";
    }

    public resetSearchCriteriaYears() {
        this.searchCriteria["selectedYear"] = "pleaseSelect";
        this.searchCriteria["years"]["name"] = "pleaseSelect";
        this.searchCriteria["years"]["value"] = "Please Select Year";
    }

    public resetSearchCriteriaBrands() {
        this.searchCriteria["selectedBrand"] = "pleaseSelect";
        this.searchCriteria["brands"]["name"] = "pleaseSelect";
        this.searchCriteria["brands"]["value"] = "Please Select Brand";
    }

    public resetSearchCriteriaModels() {
        this.searchCriteria["selectedModel"] = "pleaseSelect";
        this.searchCriteria["models"]["name"] = "pleaseSelect";
        this.searchCriteria["models"]["value"] = "Please Select Model";
    }

    public resetSearchCriteriaMMC() {
        this.searchCriteria["selectedMmc"] = "pleaseSelect";
        this.searchCriteria["mmc"]["name"] = "pleaseSelect";
        this.searchCriteria["mmc"]["value"] = "Please Select MMC Code";
    }

    /**********************************************************************/

    public resetCloneSearchCriteriaYears() {
        this.cloneTargetSearchCriteria["selectedYear"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["years"]["name"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["years"]["value"] = "Please Select Year";
    }

    public resetCloneSearchCriteriaBrands() {
        this.cloneTargetSearchCriteria["selectedBrand"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["brands"]["name"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["brands"]["value"] = "Please Select Year";
    }

    public resetCloneSearchCriteriaModels() {
        this.cloneTargetSearchCriteria["selectedModel"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["models"]["name"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["models"]["value"] = "Please Select Year";
    }

    public resetCloneSearchCriteriaMMC() {
        this.cloneTargetSearchCriteria["selectedMmc"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["mmc"]["name"] = "pleaseSelect";
        this.cloneTargetSearchCriteria["mmc"]["value"] = "Please Select Year";
    }


    public resetSearchCriteria() {
        this.searchCriteria = {
            "selectedYear":  "pleaseSelect",
            "selectedCountry": "pleaseSelect",
            "selectedBrand": "pleaseSelect",
            "selectedModel": "pleaseSelect",
            "selectedMmc": "pleaseSelect",
            "dataLoaded": false,
            "searchResultLoaded": false,
            "getXmlFilesLoaded": false,
            "years": [{
                "name" : "pleaseSelect",
                "value" : "Please Select"
            }],
            "countries": [{
                "name" : "pleaseSelect",
                "value" : "Please Select"
            }],
            "brands": [{
                "name" : "pleaseSelect",
                "value" : "Please Select"
            }],
            "models": [{
                "name" : "pleaseSelect",
                "value" : "Please Select"
            }],
            "mmc": [{
                "name" : "pleaseSelect",
                "value" : "Please Select"
            }]
        };

        return this.searchCriteria;
    }

    public isEmpty(obj) {
        for(var key in obj) {
            if(obj.hasOwnProperty(key))
                return false;
        }
        return true;
    }

    public async checkLoading(item){
        let loading = false;
        if(!item){
            loading = true;
        }else{
            loading = false;
        }
        return loading;
    }
}