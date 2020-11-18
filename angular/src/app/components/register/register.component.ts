import { Component, OnInit } from '@angular/core';
import { User } from 'src/app/models/user';
import { UserService } from 'src/app/services/user.service';

@Component({
  selector: 'register',
  templateUrl: './register.component.html',
  styleUrls: ['./register.component.css'],
  providers: [UserService]
})
export class RegisterComponent implements OnInit {
  public page_title: string;
  public user: User;
  public status: string;

  constructor(
    private _userService: UserService
  ) {
    this.page_title = 'Register';
    this.user = new User(1, 'ROLE_USER', '', '', '', '', '', '');
  }

  ngOnInit(): void {
  }

  onSubmit(form) {
    this._userService.register(this.user).subscribe(
      response => {
        if (response.status == "success") {
          this.status = response.status;
        } else {
          this.status = 'error';
        }

        form.reset();
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    );

  }
}
