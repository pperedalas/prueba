import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { User } from 'src/app/models/user';
import { UserService } from '../../services/user.service';

@Component({
  selector: 'login',
  templateUrl: './login.component.html',
  styleUrls: ['./login.component.css'],
  providers: [UserService]
})
export class LoginComponent implements OnInit {
  public page_title: string;
  public user: User;
  public status: string;
  public token;
  public identity;
  public url;

  constructor(
    private _userService: UserService,
    private _router: Router,
    private _route: ActivatedRoute
  ) {
    this.page_title = 'Login';
    this.user = new User(1, 'ROLE_USER', '', '', '', '', '', '');
    this.url = 'http://localhost:4200/';
  }

  ngOnInit() {
    // Exit when ['sure'] in logout
    this.logout();
  }

  onSubmit(_form) {
    this._userService.signup(this.user).subscribe(
      response => {
        // Token
        if (response.status != 'error') {
          this.status = 'success';
          this.token = response;

          // Object with logged user
          this._userService.signup(this.user, true).subscribe(
            response => {
              if (response.status != 'error') {
                this.identity = response;

                // Logged user data persistance
                localStorage.setItem('token', this.token);

                localStorage.setItem('identity', JSON.stringify(this.identity));
              }
              window.location.href = this.url;
            },
            error => {
              this.status = 'error';
              console.log(<any>error);
            }
          );
        }else{
          console.log(response);
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    );

  }

  logout(){
    this._route.params.subscribe(params => {
      let logout = params['sure'];

      if(logout == 1){
        localStorage.removeItem('identity');
        localStorage.removeItem('token');

        this.identity = null;
        this.token = null;

        // Redirect to index
        /* this._router.navigate(['index']); */
        window.location.href = this.url;

      }
    })
  }
}