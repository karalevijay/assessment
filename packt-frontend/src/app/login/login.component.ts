import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute } from '@angular/router';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { HttpClient} from '@angular/common/http';
import { first } from 'rxjs/operators';
import { AlertService, AuthenticationService } from '../_services';
import { COMMON_API } from '../common_api';
import { environment } from 'src/environments/environment';
import { HttpHeaders } from '@angular/common/http';

const httpOptions = {
  headers: new HttpHeaders({
    'Content-Type':  'application/json',
    Authorization: 'my-auth-token'
  })
};

@Component({ templateUrl: 'login.component.html' })
export class LoginComponent implements OnInit {
    loginForm: FormGroup;
    loading = false;
    submitted = false;
    returnUrl: string;
    public COMMON_API = COMMON_API;
    public data: any;
    public env = environment;
    public error_status = 'ERROR';
    public error_msg = '';

    constructor(
        public httpClient: HttpClient,
        private formBuilder: FormBuilder,
        private route: ActivatedRoute,
        private router: Router,
        private authenticationService: AuthenticationService,
        private alertService: AlertService
    ) {
        // redirect to home if already logged in
        if (this.authenticationService.currentUserValue) {
            this.router.navigate(['/']);
        }
    }

    ngOnInit() {
        this.loginForm = this.formBuilder.group({
            username: ['', Validators.required],
            password: ['', Validators.required]
        });

        // get return url from route parameters or default to '/'
        this.returnUrl = this.route.snapshot.queryParams['returnUrl'] || '/';
    }

    // convenience getter for easy access to form fields
    get f() { return this.loginForm.controls; }

    onSubmit() {
        this.submitted = true;
        
        // reset alerts on submit
        // this.alertService.clear();

        // stop here if form is invalid
        if (this.loginForm.invalid) {
            return;
        }
        let data = {
            email: this.f.username.value,
            password: this.f.password.value
        };
        let requestUrl = this.env.LOCAL_API_ENDPOINT + "/" + this.COMMON_API.login;
        console.log(requestUrl);
        this.loading = true;
       
        this.authenticationService.login(requestUrl, data)
            .pipe(first())
            .subscribe(
                data => {
                    if(data.status != this.error_status){
                        this.router.navigate(['/home']);
                        this.loading = false;

                    } else {
                        this.error_msg = data.messages[0];
                        this.loading = false;
                        this.router.navigate(['/login']);
                    }
                }
            );
                

    }
}
