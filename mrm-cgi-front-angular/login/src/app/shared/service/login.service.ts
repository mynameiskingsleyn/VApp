import { Injectable } from '@angular/core';
import { BaseService } from './base.service';
import { ApplicationConfiguration } from '../applicationconfiguration/application.configuration';
import { HttpClient } from '@angular/common/http';


@Injectable()
export class LoginService extends BaseService {

        constructor(httpClient: HttpClient, private applicationConfiguration: ApplicationConfiguration) {
                super(httpClient);
                this.url = this.applicationConfiguration.getBaseDataUrl() + "login";
        }

        async loginUser(loginCredentials: any) {
                return await super.sendPostRequest(loginCredentials);
        }
}    