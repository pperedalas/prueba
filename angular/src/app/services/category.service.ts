import { Injectable, Testability } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
/* import { Category } from '../models/category'; */
import { global } from './global';

@Injectable()
export class CategoryService {
    public url: string;
    public token;
    public identity;
    public status;

    constructor(
        private _http: HttpClient
    ) {
        this.url = global.url;
    }

    create(token, category):Observable<any>{
        let json = JSON.stringify(category);
        let params = "json="+json;

        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded')
                                        .set('Authorization', token);

        return this._http.post(this.url + 'category', params, {headers: headers});
    }

    getAll():Observable<any>{
        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
        return this._http.get(this.url + 'category', {headers: headers});
    }

    getOne(id):Observable<any>{
        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
        
        return this._http.get(this.url + 'category/' + id, {headers: headers});
    }

    getPosts(id):Observable<any>{
        let headers = new HttpHeaders().set('Content-Type', 'application/x-www-form-urlencoded');
        return this._http.get(this.url + 'post/category/' + id, {headers: headers});
    }
}