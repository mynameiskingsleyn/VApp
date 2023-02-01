import { HttpClient } from '@angular/common/http';
import { Injectable, Inject } from '@angular/core';
import { ApplicationConfiguration } from '../applicationconfiguration/application.configuration';
import { BaseService } from './base.service';
import { DOCUMENT } from '@angular/common';


@Injectable()
export class I18nService extends BaseService {
    public XMLEDITOR_MESSAGES = 'xmlEditor.messages';

    protected dataWithKey: any = {};

    constructor(httpClient: HttpClient,
            protected applicationConfiguration: ApplicationConfiguration,
            @Inject(DOCUMENT) protected document: Document) {
        super(httpClient, applicationConfiguration, document);
    }

    async getI18nForXmlEditor() {
        if (this.dataWithKey[this.XMLEDITOR_MESSAGES]) {
            return this.dataWithKey[this.XMLEDITOR_MESSAGES];
        }
        else {
            this.url = this.applicationConfiguration.getBaseDataUrl() + "services/i18n/" + this.XMLEDITOR_MESSAGES;
            let messages = await this.getDataWithURL();
            this.dataWithKey[this.XMLEDITOR_MESSAGES] = messages;
            return this.dataWithKey[this.XMLEDITOR_MESSAGES];
        }
    }

    private addMessagesWhileDeveloping(messages: any) {
        /* Move these messages to messages store after initial testing and development */
        messages["footerContent"] = "Get and Replace footer content.";
        messages["headerTitle"] = "XML Editor";
        messages["homeSearch"] = "Search XML";
        messages["editXml"] = "Edit XML";
        messages["viewXml"] = "View XML";
        messages["cloneXml"] = "Clone XML";
        messages["deleteXml"] = "Delete XML";
        messages["year"] = "Year";
        messages["country"] = "Country";
        messages["brand"] = "Brand";
        messages["model"] = "Model";
		messages["mmc"] = "MMC Code";
        messages["search"] = "Search";
        messages["getFiles"] = "Get XML Files";
        messages["standardFiles"] = "Standard XML Files";
        messages["layerFiles"] = "Layer XML Files";
		messages["Exterior"] = "Exterior";
		messages["interior"] = "Interior";
		messages["LayersAngle"] = "Layer Angle";

        messages["reset"] = "Reset";
        messages["submit"] = "Submit";
        messages["deleteStandards"] = "Delete Standards";
        messages["deleteLayers"] = "Delete Layers";
        messages["cloneStandards"] = "Clone Standards";
        messages["cloneLayers"] = "Clone Layers";
        messages["up"] = "Up";
        messages["down"] = "Down";
        messages["delete"] = "Del";
        messages["clone"] = "Clone";
    }
}
