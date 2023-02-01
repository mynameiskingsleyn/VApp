import { Component, Inject, Input, ElementRef, ChangeDetectionStrategy } from '@angular/core';
import { DOCUMENT } from '@angular/common';
import { I18nService}  from '../shared/service/i18n.service';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';


@Component({
    selector: 'headerview',
    templateUrl: './header.html',
    changeDetection: ChangeDetectionStrategy.Default//,
})

export class Header {
    public dataLoaded = false;
    public messages: any = {};

	constructor(public applicationConfiguration: ApplicationConfiguration, public i18nService: I18nService) {

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
}
