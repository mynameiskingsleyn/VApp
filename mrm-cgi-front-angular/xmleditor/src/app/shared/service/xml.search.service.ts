import { Injectable, Inject } from '@angular/core';
import { BaseService } from './base.service';
import { ApplicationConfiguration } from '../applicationconfiguration/application.configuration';
import { HttpClient } from '@angular/common/http';
import { DOCUMENT } from '@angular/common';

@Injectable()
export class XmlSearchService extends BaseService {
        public YEARS = 'years';
        public GETALLYEARS = 'getAllYears';
        public COUNTRIES = 'countries';
        public BRANDS = 'brands';
        public GETALLBRANDS = 'getAllBrands';
        public MODELS = 'models';
        public GETALLMODELS = 'getAllModels';
        public MMCCODES = 'mmc-codes';
        public SEARCH_XML = 'searchXml';
        public GETFILES = 'getFiles';
        public GETRAWXML = 'convertJsonToXml';
        public GETJSONXML = 'convertXmlToJson';
        public COMMIT = 'commitFiles'
        public result = {};
        public flagSearchBtn = false;

        constructor(httpClient: HttpClient,
                    protected applicationConfiguration: ApplicationConfiguration,
                    @Inject(DOCUMENT) protected document: Document) {
                super(httpClient, applicationConfiguration, document);
        }

        public async getYears() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/criteria/" + this.YEARS;
                let searchCriteria = this.applicationConfiguration.getSearchCriteria();
                let criteria = {};
                criteria["country"] = searchCriteria['selectedCountry'];
                return await this.sendPostRequest(criteria);
        }

        public async getAllYears() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/xml/" + this.GETALLYEARS;
                return await this.getDataWithURL();
        }

        public async getCountries() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/criteria/" +  this.COUNTRIES;
                return await this.getDataWithURL();
        }

        public async getBrands() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/criteria/" + this.BRANDS;

                let searchCriteria = this.applicationConfiguration.getSearchCriteria();
                let criteria = {};
                criteria["country"] = searchCriteria['selectedCountry'];
                criteria["year"] = searchCriteria['selectedYear'];
                return await this.sendPostRequest(criteria);
        }

        public async getAllBrands() {
                //this.url = this.applicationConfiguration.getBaseDataUrl() + "search/criteria/" + this.GETALLBRANDS;
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/xml/" + this.GETALLBRANDS;
                return await this.getDataWithURL();
        }

        public async getModels() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/criteria/" + this.MODELS;

                let searchCriteria = this.applicationConfiguration.getSearchCriteria();
                let criteria = {};
                criteria["country"] = searchCriteria['selectedCountry'];
                criteria["year"] = searchCriteria['selectedYear'];
                criteria["brand"] = searchCriteria['selectedBrand'];
                return await this.sendPostRequest(criteria);
        }

        public async getAllModels() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/xml/" + this.GETALLMODELS;
                return await this.getDataWithURL();
        }

        public async getMmc() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/criteria/" + this.MMCCODES;

                let searchCriteria = this.applicationConfiguration.getSearchCriteria();
                let criteria = {};
                criteria["country"] = searchCriteria['selectedCountry'];
                criteria["year"] = searchCriteria['selectedYear'];
                criteria["brand"] = searchCriteria['selectedBrand'];
                criteria["model"] = searchCriteria['selectedModel'];
                return await this.sendPostRequest(criteria);

        }

        public async searchXml() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/xml/" + this.SEARCH_XML;

                let searchCriteria = this.applicationConfiguration.getSearchCriteria();
                let criteria = {};
                criteria["country"] = searchCriteria['selectedCountry'];
                criteria["year"] = searchCriteria['selectedYear'];
                criteria["brand"] = searchCriteria['selectedBrand'];
                criteria["model"] = searchCriteria['selectedModel'];
                criteria["mmc_code"] = searchCriteria['selectedMmc'];

                return await this.sendPostRequest(criteria);
        }

        public async getXmlFiles() {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/xml/" + this.GETFILES;
                let searchCriteria = this.applicationConfiguration.getSearchCriteria();
                let criteria = {};
                criteria["country"] = searchCriteria['selectedCountry'];
                criteria["year"] = searchCriteria['selectedYear'];
                criteria["brand"] = searchCriteria['selectedBrand'];
                criteria["model"] = searchCriteria['selectedModel'];
                criteria["mmc_code"] = searchCriteria['selectedMmc'];

                let xmlSearchResult = this.applicationConfiguration.getXmlSearchResult();
                let temp = {"StandardFiles" : []};
                for (let standardFile of xmlSearchResult["data"]["StandardFiles"]) {
                        if (standardFile["getFile"] == true) {
                                temp["StandardFiles"].push(standardFile);
                        }
                }
                criteria["StandardFiles"] = temp['StandardFiles'];
                return await this.sendPostRequest(criteria);
        }

        public async getRawXml(jsonXml) {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/xml/" + this.GETRAWXML;
                return await this.sendPostRequest(jsonXml);
        }

        public async rawXmlToJson(editRawXml) {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "search/xml/" + this.GETJSONXML;
                return await this.sendPostRequest(editRawXml);
        }

        public async commitChanges(logInfo){
                this.url = this.applicationConfiguration.getBaseDataUrl()  + this.COMMIT;
                return await this.sendPostRequest(logInfo);
        }
}
