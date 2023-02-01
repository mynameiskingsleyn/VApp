import { Injectable, Inject } from '@angular/core';
import { BaseService } from './base.service';
import { ApplicationConfiguration } from '../applicationconfiguration/application.configuration';
import { HttpClient } from '@angular/common/http';
import { DOCUMENT } from '@angular/common';

@Injectable()
export class XmlEditService extends BaseService {
        constructor(httpClient: HttpClient,
                protected applicationConfiguration: ApplicationConfiguration,
                @Inject(DOCUMENT) protected document: Document) {
            super(httpClient, applicationConfiguration, document);
        }

        public async editXml(editJson) {
            this.url = this.applicationConfiguration.getBaseDataUrl() + "xml/update";
            return await this.sendPostRequest(editJson);
        }
}