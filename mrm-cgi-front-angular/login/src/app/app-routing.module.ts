import { Routes,RouterModule } from '@angular/router';
import { NgModule } from '@angular/core';
import { LoginForm } from './loginform/login.form';

const appRoutes: Routes = [
    {path: 'LoginForm', component: LoginForm},
    {path: '', redirectTo: '/LoginForm', pathMatch: 'full'}
];

@NgModule({
    imports: [RouterModule.forRoot(appRoutes, { useHash: true })],
    exports: [RouterModule]
})    

export class AppRoutingModule {
    
}