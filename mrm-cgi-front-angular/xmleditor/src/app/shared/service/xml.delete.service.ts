import { Injectable, Inject } from '@angular/core';
import { BaseService } from './base.service';
import { ApplicationConfiguration } from '../applicationconfiguration/application.configuration';
import { HttpClient } from '@angular/common/http';
import { DOCUMENT } from '@angular/common';

@Injectable()
export class XmlDeleteService extends BaseService {
        constructor(httpClient: HttpClient,
                protected applicationConfiguration: ApplicationConfiguration,
                @Inject(DOCUMENT) protected document: Document) {
            super(httpClient, applicationConfiguration, document);
        }

        public async deleteXml(deleteJson) {
                this.url = this.applicationConfiguration.getBaseDataUrl() + "deleteXml";
		return await this.sendPostRequest(deleteJson);
        }
}