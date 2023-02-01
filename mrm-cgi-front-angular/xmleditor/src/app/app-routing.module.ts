import { Routes,RouterModule } from '@angular/router';
import { NgModule } from '@angular/core';
import { HomeSearch } from './homesearch/home.search';
import { EditXml } from './editxml/edit.xml';
import { ViewXml } from './viewxml/view.xml';
import { CloneXml } from './clonexml/clone.xml';
import { DeleteXml } from './deletexml/delete.xml';

const appRoutes: Routes = [
    {path: 'HomeSearch', component: HomeSearch},
    {path: '', redirectTo: '/HomeSearch', pathMatch: 'full'},
    {path: 'EditXml', component: EditXml},
    {path: 'ViewXml', component: ViewXml},
    {path: 'CloneXml', component: CloneXml},
    {path: 'DeleteXml', component: DeleteXml}
];

@NgModule({
    imports: [RouterModule.forRoot(appRoutes, { useHash: true })],
    //imports: [RouterModule.forRoot(appRoutes)],
    exports: [RouterModule]
})    

export class AppRoutingModule {
    
}