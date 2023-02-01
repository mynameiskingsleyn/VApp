import { Injectable } from '@angular/core';
import { BaseService } from './base.service';
import { ApplicationConfiguration } from '../applicationconfiguration/application.configuration';
import { HttpClient } from '@angular/common/http';


@Injectable()
export class I18nService extends BaseService {
    public LOGIN_MESSAGES = 'login.messages';

    protected dataWithKey: any = {};

    constructor(httpClient: HttpClient, private applicationConfiguration: ApplicationConfiguration) {
        super(httpClient);
    }

    async getI18nForLogin() {
        if (this.dataWithKey[this.LOGIN_MESSAGES]) {
            return this.dataWithKey[this.LOGIN_MESSAGES];
        }
        else {
            this.url = this.applicationConfiguration.getBaseDataUrl() + "services/i18n/" + this.LOGIN_MESSAGES;
            return await this.getDataWithURL();
        }
    }
}
