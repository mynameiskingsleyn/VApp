import { Component, Input, Inject } from '@angular/core';
import { I18nService } from '../shared/service/i18n.service';
import { XmlSearchService } from '../shared/service/xml.search.service';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';
import { Router } from '@angular/router';

@Component({
    selector: 'homesearch',
    templateUrl: './home.search.html'
})
export class HomeSearch {
    public messages: any = {};

    public selectFiles : any;

    public searchFlag : any = false;
    public getFilesFlag : any = false;
    public viewFilesFlag: any = false;
    public searchResultFlag: any = false;
    public getFilesEmpty: any = false;
    public filesDontExist: any = true;

    public canReset: any = false;

    public successMessage: any = "";

    public errorMessage: any="";

    public nonExistFiles: any = [];

    public filesMissing: any = false;

    public byHandMMC: any = "";

    public svnUser: any ="";

    public svnPassword: any="";

    public svnComment: any="";

    public isLoading: boolean;

    public commitFlaged: boolean =false;


    @Input() public xmlSearchResult: any = {};

    @Input() public searchCriteria: any;

    constructor(public i18nService: I18nService,
                private applicationConfiguration: ApplicationConfiguration,
                private xmlSearchService: XmlSearchService,
                private router: Router) {
        this.searchCriteria = applicationConfiguration.getSearchCriteria();
        this.xmlSearchResult = applicationConfiguration.getXmlSearchResult();
        this.messages = applicationConfiguration.getMessages();
        if (this.searchCriteria.dataLoaded == false) {
            this.loadData();
        }

        this.searchFlag = this.checkAllDropdowns();
        this.viewFilesFlag = true;
        //this.canReset = this.resetAble();
    }

    ngOnInit(){
        this.canReset = this.resetAble();
        this.isLoading = false;
    }


    async loadData() {
        this.messages = await this.i18nService.getI18nForXmlEditor();
        //console.log(this.messages);
        this.applicationConfiguration.setMessages(this.messages);
        this.searchCriteria['countries'] = await this.xmlSearchService.getCountries();
        this.searchCriteria.dataLoaded = true;
        //this.isLoading = true;
    }

    async resetSearch() {
        this.resetMessages();
        this.applicationConfiguration.resetSearchCriteria();
        this.applicationConfiguration.resetSearchCriteriaYears();
        this.applicationConfiguration.resetSearchCriteriaBrands();
        this.applicationConfiguration.resetSearchCriteriaModels();
        this.applicationConfiguration.resetSearchCriteriaMMC();
        this.searchCriteria = this.applicationConfiguration.getSearchCriteria();
        this.loadData();
        this.viewFilesFlag = false;
        this.searchFlag = false;
        this.getFilesFlag = false;
        this.selectFiles = false;
        this.canReset = this.resetAble();
    }

    /* async yearData(year) {
        this.searchCriteria.years = await this.xmlSearchService.getYears();
    }
    async brandData() {
        this.searchCriteria.brands = await this.xmlSearchService.getBrands();
    }
    async modelData() {
        this.searchCriteria.models = await this.xmlSearchService.getModels();
    } */

    async selectCountry() {
        this.isLoading = true;
        this.resetMessages();
        this.searchFlag = false;
        this.getFilesFlag = false;
        this.viewFilesFlag = false;
        this.nonExistFiles = [];

        this.applicationConfiguration.resetSearchCriteriaYears();
        this.applicationConfiguration.resetSearchCriteriaBrands();
        this.applicationConfiguration.resetSearchCriteriaModels();
        this.applicationConfiguration.resetSearchCriteriaMMC();

        this.searchCriteria['years'] = await this.xmlSearchService.getYears();
        let loading = await this.applicationConfiguration.checkLoading(this.searchCriteria['years']);
        this.isLoading = loading;
        //this.searchCriteria['years'] = await this.xmlSearchService.getAllYears();
    }

    async selectYear() {
        this.canReset = this.resetAble();
        this.resetMessages();
        this.searchFlag = false;
        this.getFilesFlag = false;
        this.viewFilesFlag = false;
        this.nonExistFiles = [];
        this.isLoading = true;

        this.applicationConfiguration.resetSearchCriteriaBrands();
        this.applicationConfiguration.resetSearchCriteriaModels();
        this.applicationConfiguration.resetSearchCriteriaMMC();

        if (this.searchCriteria['years'] == 'pleaseSelect') return;
        this.searchCriteria['brands'] = await this.xmlSearchService.getBrands();
        //this.searchCriteria['brands'] = await this.xmlSearchService.getAllBrands();
        let loading = await this.applicationConfiguration.checkLoading(this.searchCriteria['brands']);
        this.isLoading = loading;

        this.filesDontExist = false;
    }

    async selectBrand() {
        this.resetMessages();
        this.searchFlag = false;
        this.getFilesFlag = false;
        this.viewFilesFlag = false;
        this.nonExistFiles = [];
        this.isLoading = true;

        this.applicationConfiguration.resetSearchCriteriaModels();
        this.applicationConfiguration.resetSearchCriteriaMMC();

        if (this.searchCriteria['brands'] == 'pleaseSelect') return;
        this.searchCriteria['models'] = await this.xmlSearchService.getModels();
        //this.searchCriteria['models'] = await this.xmlSearchService.getAllModels();
        let loading = await this.applicationConfiguration.checkLoading(this.searchCriteria['models']);
        this.isLoading = loading;

        //this.selectFiles = false;
        this.filesDontExist = false;

        if (this.checkAllDropdowns())
            this.searchFlag = true;
    }

    async selectModel() {
        this.resetMessages();
        this.isLoading = true;
        this.viewFilesFlag = false;
        this.getFilesFlag = false;
        this.nonExistFiles = [];

        this.applicationConfiguration.resetSearchCriteriaMMC();

        if (this.searchCriteria['models'] == 'pleaseSelect') return;
        this.searchCriteria['mmc'] = await this.xmlSearchService.getMmc();
        let loading = await this.applicationConfiguration.checkLoading(this.searchCriteria['mmc']);
        this.isLoading = loading;
        //this.selectFiles = false;
        this.filesDontExist = false;
    }

    async searchXml() {
        this.resetMessages();
        this.searchCriteria.searchResultLoaded = false;
        this.isLoading = true;
        if (this.searchCriteria['selectedMmc'] == 'pleaseSelect' && this.byHandMMC.trim().length > 0) {
            this.searchCriteria['selectedMmc'] = this.byHandMMC.trim();
        }

        let temp = await this.xmlSearchService.searchXml();
        let loading = await this.applicationConfiguration.checkLoading(temp);
        this.isLoading = loading;
        this.applicationConfiguration.setXmlSearchResult(temp);
        this.xmlSearchResult = this.applicationConfiguration.getXmlSearchResult();
        this.applicationConfiguration.getResetXmlDeleteRequest();
        this.applicationConfiguration.getResetXmlCloneRequest();
        this.searchCriteria.searchResultLoaded = true;

        if(this.xmlSearchResult.data["StandardFiles"].length <= 0) {
            this.searchResultFlag = true;
            this.errorMessage = this.messages['noResultsFound'];
        } else {
            this.searchResultFlag = false;
        }

        //this.selectFiles = false;
        this.filesDontExist = false;
        this.nonExistFiles = [];

        this.viewFilesFlag = true;

        if (this.checkAllDropdowns())
            this.getFilesFlag = true;
    }

    async getXmlFiles() {
        this.resetMessages();
        this.isLoading = true;
        this.applicationConfiguration.setXmlSearchResult(this.xmlSearchResult['data']);
        let getXmlFilesResult = await this.xmlSearchService.getXmlFiles();
        let loading = await this.applicationConfiguration.checkLoading(getXmlFilesResult);
        this.isLoading = loading;

        if(getXmlFilesResult["StandardFiles"].length <= 0)
            this.getFilesEmpty = true;
        else
            this.getFilesEmpty = false;

        this.applicationConfiguration.setXmlFilesSearchResult(getXmlFilesResult);
        let temp = this.applicationConfiguration.getResetXmlEditRequest();

        this.nonExistFiles = [];
        let allFilesRetrived = true;
        let tempFlag = true;
        //console.log('retrieved file messages is '+ this.messages['filesRetrieved']);
        for (var standardFile of temp["data"]["StandardFiles"]) {
            if (standardFile["rawXml"].length < 5) {
                if (standardFile["exterior"] == "0") {
                    this.nonExistFiles.push(standardFile["name"] + " / Interior");
                } else {
                    this.nonExistFiles.push(standardFile["name"] + " / Exterior");
                }
                allFilesRetrived = false;
            }
            for (var layerFile of standardFile["layerFiles"]) {
                if (layerFile["rawXml"].length < 5) {
                    let intExt = layerFile["exterior"] == "0"? "Interior" : "Exterior";
                    this.nonExistFiles.push(layerFile["name"] + " / "+intExt+" / " + layerFile["layer_angle"]);
                    //this.selectFiles = false;
                    allFilesRetrived = false;
                }
            }
        }
        if(this.searchResultFlag){
            this.errorMessage = this.messages['noResultsFound'];
        }
        else if(this.getFilesEmpty){
            this.errorMessage = this.messages['noFilesSelected'];
        }else{
            if(allFilesRetrived){
                this.successMessage = this.messages['filesRetrieved'];
            }else{
                this.filesMissing = true;
                this.errorMessage = this.messages['filesDontExist'];
            }
        }
        //console.log(this.messages);
        //console.log(this.errorMessage);
        this.searchCriteria.getXmlFilesLoaded = true;
        this.viewFilesFlag = true;
        //this.filesDontExist = true;
    }

    public editXml() {
        this.router.navigate(['EditXml']);
    }

    public viewXml() {
        this.router.navigate(['ViewXml']);
    }

    public cloneXml() {
        this.router.navigate(['CloneXml']);
    }

    public deleteXml() {
        this.router.navigate(['DeleteXml']);
    }

    public selectAllLayers() {
        for (var standardFile of this.xmlSearchResult.data["StandardFiles"]) {
            let layerFiles = standardFile["layerFiles"];
            for (var layerFile of layerFiles) {
                layerFile["getFile"] = standardFile["getFile"];
            }
        }
    }

    public checkAllDropdowns() {
        if (this.searchCriteria["selectedCountry"] == "pleaseSelect")
            return false;

        if (this.searchCriteria["selectedYear"] == "pleaseSelect")
            return false;

        if (this.searchCriteria["selectedBrand"] == "pleaseSelect")
            return false;

        //if (this.searchCriteria["selectedModel"] == "pleaseSelect")
        //    return false;

        return true;
    }

    public commitChanges()
    {
        this.resetMessages();
        this.commitFlaged = true;
    }

    async  submitCommit()
    {
        this.resetMessages();

        this.commitFlaged = true;
        if(!this.svnUser || !this.svnPassword){
            this.errorMessage = 'SVN username and password is required';
            return
        }
        this.isLoading = true;
        let data = {
            "username":this.svnUser,
            "pass": this.svnPassword,
            "comment": this.svnComment
        };

        //console.log(data);

        let commitResult = await this.xmlSearchService.commitChanges(data);
        let loading = await this.applicationConfiguration.checkLoading(commitResult);
        this.isLoading = loading;
        //console.log(commitResult);
        let status = commitResult['status'];
        if(status == 'success'){
            this.successMessage = commitResult['message']
        }else{
            this.errorMessage = commitResult['errorMessage'];
        }

    }

    public cancelCommit(){
        this.commitFlaged = false;
        this.resetMessages();
    }

    public resetMessages()
    {
        this.commitFlaged = false;
        this.errorMessage ='';
        this.successMessage = '';
        this.filesMissing = false;
        this.isLoading = false;
    }

    public resetAble()
    {
        let Criteria = this.searchCriteria;
        let resetable =  Criteria['selectedYear'] !== "pleaseSelect";
        return resetable;
    }

    public ownerHere(owner: string): string{
        let answer = '';
        if(owner != "Admin"){
            answer = 'Last updated by: '+owner;
        }
        return answer;
    }

}