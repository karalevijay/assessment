import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { BehaviorSubject, Observable } from 'rxjs';
import { map } from 'rxjs/operators';

import { User } from '../_models';

@Injectable({ providedIn: 'root' })
export class AuthenticationService {
    private currentUserSubject: BehaviorSubject<User>;
    public currentUser: Observable<User>;

    constructor(private http: HttpClient) {
        this.currentUserSubject = new BehaviorSubject<User>(JSON.parse(localStorage.getItem('currentUser')));
        this.currentUser = this.currentUserSubject.asObservable();
    }

    public get currentUserValue(): User {
        return this.currentUserSubject.value;
    }

    login(url, data) {
        let headers = new HttpHeaders({ 'Content-Type': 'application/json' });
        
        return this.http.post<any>(url, data, { headers })
            .pipe(map(user => {
                console.log("LOGIN----", user);
                if (user.status != 'ERROR') {
                    // store user details and jwt token in local storage to keep user logged in between page refreshes
                    localStorage.setItem('currentUser', JSON.stringify(user));
                    localStorage.setItem('app_token', JSON.stringify(user.data.app_token));
                    this.currentUserSubject.next(user);
                    return user;
                }
                return user;
            }));
    }

    logout() {
        // remove user from local storage and set current user to null
        localStorage.removeItem('currentUser');
        this.currentUserSubject.next(null);
    }

    makeAjax(url, request_method = 'get', data = {}) {
        var headers = new HttpHeaders({ 'Content-Type': 'application/json' });
        // if(url != 'login' || url != 'register'){
        //     headers = new HttpHeaders({
        //         'Content-Type': 'application/json',
        //         'Authorization': `Bearer ${localStorage.getItem('app_token')}`
        //       });
        // }
        if (request_method == 'post') {
            return this.http.post<any>(url, JSON.stringify(data),  { headers: headers })
                .pipe(map(data => {
                    if (data.status != 'ERROR') {
                        // store user details and jwt token in local storage to keep user logged in between page refreshes
                        return JSON.stringify(data);
                    }
                    return data;
                }));
        } else {
            return;
        }
    }
}