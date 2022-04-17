import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { first } from 'rxjs/operators';
import { environment } from 'src/environments/environment';
import { User } from '../_models';
import { COMMON_API } from '../common_api';
import { UserService, AuthenticationService } from '../_services';

@Component({ templateUrl: 'home.component.html' })
export class HomeComponent implements OnInit {
    currentUser: User;
    users = [];
    public env = environment;
    public COMMON_API = COMMON_API;
    public error_msg = '';
    public loading = false;
    constructor(
        private authenticationService: AuthenticationService,
        private userService: UserService,
        private router: Router,
    ) {
        this.currentUser = this.authenticationService.currentUserValue;
    }

    ngOnInit() {
        this.loadAllUsers();
    }

    deleteUser(id: number) {
    }

    private loadAllUsers() {
        console.log("--loadAllUsers---");
        this.loading = true;
        let requestData = {
            id: ''
        };
        let requestUrl = this.env.LOCAL_API_ENDPOINT + "/" + this.COMMON_API.loadAllUsers;
        this.authenticationService.makeAjax(requestUrl, 'post', requestData)
            .pipe(first())
            .subscribe(
                data => {
                    console.log("------data-------",data);
                    if (data.status != 'ERROR') {
                        // this.router.navigate(['/login']);
                        this.users = data;
                        this.loading = false;

                    } else {
                        this.error_msg = data.messages[0];
                        this.loading = false;
                        // this.router.navigate(['/register']);
                    }
                }
            );
    }
}