import { Injectable, Component, NgModule } from "@angular/core";
import { CookieService } from "ngx-cookie-service";

@Injectable()
export class ApplicationConfiguration {
  private baseAppUrl: string = "";
  private baseDataUrl: string = "";

  constructor(private _cookieService: CookieService) {}

  public setBaseDataUrl(baseDataUrl: string) {
    this.baseDataUrl = baseDataUrl;
  }

  public setBaseApplicationUrl(baseAppUrl: string) {
    this.baseAppUrl = baseAppUrl;
  }

  public getBaseDataUrl() {
    return this.baseDataUrl;
  }

  public getBaseApplicationUrl() {
    return this.baseAppUrl;
  }

  public getCookieString(cookieName: string) {
    return this._cookieService.get(cookieName);
  }
}
