import { NgModule, Inject, Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { DOCUMENT } from '@angular/common';
import { BrowserModule } from '@angular/platform-browser';
import { AppRoutingModule } from './app-routing.module';
import { FormsModule } from '@angular/forms';
import { AppComponent } from './app.component';
import { Header } from './header/header';
import { MenuBar } from './menubar/menubar';
import { Footer } from './footer/footer';
import { CookieService } from 'ngx-cookie-service';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { I18nService } from './shared/service/i18n.service';
import { ApplicationConfiguration } from './shared/applicationconfiguration/application.configuration';
import { HomeSearch } from './homesearch/home.search';
import { EditXml } from './editxml/edit.xml';
import { ViewXml } from './viewxml/view.xml';
import { CloneXml } from './clonexml/clone.xml';
import { DeleteXml } from './deletexml/delete.xml';
import { XmlSearchService } from './shared/service/xml.search.service';
import { XmlDeleteService } from './shared/service/xml.delete.service';
import { XmlCloneService } from './shared/service/xml.clone.service';
import { XmlLogoutService } from './shared/service/xml.logout.service';
import { XmlEditService } from './shared/service/xml.edit.service';


@NgModule({
    imports: [
        BrowserModule,
        AppRoutingModule,
        FormsModule,
        HttpClientModule
    ],
    declarations: [
        AppComponent, Header, MenuBar, HomeSearch, EditXml, ViewXml, CloneXml, DeleteXml, Footer
    ],

    providers: [
        CookieService, HttpClient, ApplicationConfiguration, I18nService, XmlSearchService, XmlDeleteService, XmlCloneService, XmlLogoutService, XmlEditService
    ],
    bootstrap: [ AppComponent]
})

export class AppModule {
    constructor(@Inject(DOCUMENT) private document: any,
        private router: Router,
        private applicationConfiguration: ApplicationConfiguration,
        public i18nService: I18nService) {

        /*
        let response = await this.loginService.loginUser(this.user);
        if (response["status"] == "success") {
            let appUrl = this.applicationConfiguration.getBaseApplicationUrl() + "xmleditor/xmleditor.html";
            this.document.location.href = appUrl;
        } else {
            this.errorMessage = response["message"];
        }
        */

        var origin: string;
        if (!this.document.location.origin) {
            origin = this.document.location.protocol + "//" + this.document.location.hostname + (this.document.location.port ? ':' + this.document.location.port: '');
        } else {
            origin = this.document.location.origin;
        }


        this.applicationConfiguration.setBaseDataUrl(origin + '/api/');
        this.applicationConfiguration.setBaseApplicationUrl(origin + '/');

        this.setRouteValue();
    }

    private setRouteValue(){
        var routeValue = 'HomeSearch';
        this.router.navigate([routeValue]);
    }
}
