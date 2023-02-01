import { Component, Input, ChangeDetectionStrategy, OnInit } from '@angular/core';
import { I18nService } from '../shared/service/i18n.service';
import { ApplicationConfiguration } from '../shared/applicationconfiguration/application.configuration';

@Component({
    selector: 'menubar',
    templateUrl: './menubar.html',
    changeDetection: ChangeDetectionStrategy.Default
})

export class MenuBar {
    public dataLoaded = false;
    public messages: any = {};

    constructor(public i18nService: I18nService,
                private applicationConfiguration: ApplicationConfiguration) {
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
}
