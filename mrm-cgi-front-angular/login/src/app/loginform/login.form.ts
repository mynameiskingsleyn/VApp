import { Component, Input, Inject } from '@angular/core';
import { LoginService } from '../shared/service/login.service';
import { I18nService } from '../shared/service/i18n.service';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';
import { DOCUMENT } from '@angular/common';

@Component({
    selector: 'loginform',
    templateUrl: './login.form.html'
})
export class LoginForm {
    public dataLoaded = false;
    public messages: any = {};
    public errorMessage = "";

    public loading: boolean=false;
    @Input() public user: any = {userName: "", password: ""};
    constructor(private loginService: LoginService,
        public i18nService: I18nService,
        @Inject(DOCUMENT) private document: Document,
        private applicationConfiguration: ApplicationConfiguration) {
        
    }

    async ngOnInit() {
        if (this.dataLoaded == false) {
            await this.loadRequiredData();
        }
    }

    async loadRequiredData() {
        this.messages = await this.i18nService.getI18nForLogin();
        this.dataLoaded = true;
    }
    
    async loginUser() {
        let response = await this.loginService.loginUser(this.user);
        if (response["status"] == "success") {
            let appUrl = this.applicationConfiguration.getBaseApplicationUrl() + "xmleditor/xmleditor.html";
            this.document.location.href = appUrl;
        } else {
            this.errorMessage = response["message"];
        }
    }
}