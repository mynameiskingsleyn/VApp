import { DOCUMENT } from '@angular/common';
import { HttpClient } from '@angular/common/http';
import { Inject, Injectable } from '@angular/core';
import { ApplicationConfiguration } from '../applicationconfiguration/application.configuration';

@Injectable()
export class BaseService {
    protected url: string;

    constructor(protected httpClient: HttpClient,
                protected applicationConfiguration: ApplicationConfiguration,
                @Inject(DOCUMENT) protected document: Document) {
    }

    async getDataWithURL() {
        var promiseVar = this.httpClient.get(this.url).toPromise();
        let body = await promiseVar.then(this.extractData).catch(this.handleError);
        if (body["status"] == "failure") {
            if (body["message"] == "Cookie has expired") {
                let appUrl = this.applicationConfiguration.getBaseApplicationUrl() + "login/login.html";
                this.document.location.href = appUrl;
            }
        }
        return body;
    }

    async sendPostRequest(postData: any) {
        var promiseVar: Promise<Object>;
        promiseVar = this.httpClient.post(this.url, postData).toPromise();
        let body = await promiseVar.then(this.extractData).catch(this.handleError);
        if (body["status"] == "failure") {
            if (body["message"] == "Cookie has expired") {
                let appUrl = this.applicationConfiguration.getBaseApplicationUrl() + "login/login.html";
                this.document.location.href = appUrl;
            }
        }
        return body;
    }

    private extractData(response: Response) {
        let body = response ? response : {};
        return body;
    }

    private handleError() {
        return {};
    }
}