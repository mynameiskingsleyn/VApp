import { Component, Input, ChangeDetectionStrategy } from '@angular/core';
import {ApplicationConfiguration} from '../shared/applicationconfiguration/application.configuration';
import {I18nService} from '../shared/service/i18n.service';

declare var $:any;

@Component({
    selector: 'footerview',
    templateUrl: './footer.html',
    changeDetection: ChangeDetectionStrategy.Default
})

export class Footer {
    public dataLoaded = false;
    public messages: any = {};
	
    constructor(public applicationConfiguration: ApplicationConfiguration, public i18nService : I18nService) {
    }
      
    async ngOnInit() {
        if (this.dataLoaded == false) {
            this.messages = await this.i18nService.getI18nForLogin();
            this.dataLoaded = true;
        }
    }
}
