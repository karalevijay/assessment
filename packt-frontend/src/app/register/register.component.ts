import { Component, OnInit } from '@angular/core';
import { Router } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { first } from 'rxjs/operators';
import { environment } from 'src/environments/environment';
import { COMMON_API } from '../common_api';

import { AlertService, UserService, AuthenticationService } from '../_services';

@Component({ templateUrl: 'register.component.html' })
export class RegisterComponent implements OnInit {
    registerForm: FormGroup;
    loading = false;
    submitted = false;
    public COMMON_API = COMMON_API;
    public env = environment;
    public error_msg = '';
    constructor(
        private formBuilder: FormBuilder,
        private router: Router,
        private authenticationService: AuthenticationService,
        private userService: UserService,
        private alertService: AlertService
    ) {
        // redirect to home if already logged in
        if (this.authenticationService.currentUserValue) {
            this.router.navigate(['/']);
        }
    }

    ngOnInit() {
        this.registerForm = this.formBuilder.group({
            firstName: ['', Validators.required],
            lastName: ['', Validators.required],
            mobile: ['', Validators.required],
            address_one: ['', Validators.required],
            post_title: ['', Validators.required],
            city: ['', Validators.required],
            username: ['', Validators.required],
            password: ['', [Validators.required, Validators.minLength(6)]]
        });
    }

    // convenience getter for easy access to form fields
    get f() { return this.registerForm.controls; }

    onSubmit() {
        this.submitted = true;

        // reset alerts on submit
        this.alertService.clear();

        // stop here if form is invalid
        // if (this.registerForm.invalid) {
        //     return;
        // }

        let registerData = {
            fname: this.f.firstName.value,
            lname: this.f.lastName.value,
            mobile: this.f.mobile.value,
            address_one: this.f.address_one.value,
            city: this.f.city.value,
            post_title: this.f.post_title.value,
            email: this.f.username.value,
            password: this.f.password.value
        };

        this.loading = true;

        let requestUrl = this.env.LOCAL_API_ENDPOINT + "/" + this.COMMON_API.register;
        this.authenticationService.makeAjax(requestUrl, 'post', registerData)
            .pipe(first())
            .subscribe(
                data => {
                    if (data.status != 'ERROR') {
                        this.router.navigate(['/login']);
                        this.loading = false;

                    } else {
                        this.error_msg = data.messages[0];
                        this.loading = false;
                        this.router.navigate(['/register']);
                    }
                }
            );
    }
}
