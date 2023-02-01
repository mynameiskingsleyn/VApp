import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';

@Injectable()
export class BaseService {
    protected url: string;

    constructor(protected httpClient: HttpClient) {
    }

    async getDataWithURL(): Promise<any> {
        var promiseVar = this.httpClient.get(this.url).toPromise();
        return promiseVar.then(this.extractData).catch(this.handleError);
    }

    async sendPostRequest(postData: any): Promise<any> {
        var promiseVar: Promise<Object>;
        promiseVar = this.httpClient.post(this.url, postData).toPromise();
        return promiseVar.then(this.extractData).catch(this.handleError);
    }

    private extractData(response: Response) {
        let body = response ? response : {};
        return body;
    }

    private handleError() {
        return {};
    }
}