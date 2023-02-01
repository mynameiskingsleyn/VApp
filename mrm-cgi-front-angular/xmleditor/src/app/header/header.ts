import { DOCUMENT } from '@angular/common';
import { ChangeDetectionStrategy, Component, Inject } from '@angular/core';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';
import { I18nService } from '../shared/service/i18n.service';
import { XmlLogoutService } from './../shared/service/xml.logout.service';


@Component({
    selector: 'headerview',
    templateUrl: './header.html',
    changeDetection: ChangeDetectionStrategy.Default//,
})

export class Header {
    public dataLoaded = false;
    public messages: any = {};

    public logoutConfirmation: any = {};

    constructor(public i18nService: I18nService,
                private xmlLogoutService: XmlLogoutService,
                private applicationConfiguration: ApplicationConfiguration,
                @Inject(DOCUMENT) private document: Document) {

    }

    ngOnInit() {
        if (this.dataLoaded == false) {
            this.loadRequiredData();
        }
    }

    async loadRequiredData() {
        this.messages = await this.i18nService.getI18nForXmlEditor();
        this.dataLoaded = true;
    }

    async clickLogout() {
        this.logoutConfirmation = await this.xmlLogoutService.logout();
        if (this.logoutConfirmation["status"] == "success") {
            let appUrl = this.applicationConfiguration.getBaseApplicationUrl() + "login/login.html";
            this.document.location.href = appUrl;
        } else {
            console.log(this.logoutConfirmation);
        }
    }
}
