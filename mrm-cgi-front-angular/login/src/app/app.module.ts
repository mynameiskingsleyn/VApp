import { NgModule, Inject, Injectable } from '@angular/core';
import { Router } from '@angular/router';
import { DOCUMENT } from '@angular/common';
import { BrowserModule } from '@angular/platform-browser';
import { AppRoutingModule } from './app-routing.module';
import { FormsModule } from '@angular/forms';
import { AppComponent } from './app.component';
import { Header } from './header/header';
import { LoginForm } from './loginform/login.form';
import { Footer } from './footer/footer';
import { CookieService } from 'ngx-cookie-service';
import { HttpClientModule, HttpClient } from '@angular/common/http';
import { I18nService } from './shared/service/i18n.service';
import { ApplicationConfiguration } from './shared/applicationconfiguration/application.configuration';
import { LoginService } from './shared/service/login.service';

@NgModule({
    imports: [
        BrowserModule,
        AppRoutingModule,
        FormsModule,
        HttpClientModule
    ],
    declarations: [
        AppComponent, Header, LoginForm, Footer
    ],

    providers: [
        CookieService, HttpClient, ApplicationConfiguration, I18nService, LoginService
    ],
    bootstrap: [ AppComponent]
})
    
export class AppModule {
    constructor(@Inject(DOCUMENT) private document: any,
        private router: Router,
        private applicationConfiguration: ApplicationConfiguration,
        public i18nService: I18nService) {

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

    setRouteValue(){
        var routeValue = 'LoginForm';
        this.router.navigate([routeValue]);
    }
}
