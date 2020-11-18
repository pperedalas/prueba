import { Component, OnInit } from '@angular/core';
import { User } from 'src/app/models/user';
import { UserService } from 'src/app/services/user.service';
import { global } from '../../services/global';

@Component({
  selector: 'app-user-edit',
  templateUrl: './user-edit.component.html',
  styleUrls: ['./user-edit.component.css'],
  providers: [UserService]
})

export class UserEditComponent implements OnInit {

  public page_title: string;
  public user: User;
  public identity;
  public token;
  public status;
  public url;
  public froala_options: Object = {
    charCounterCount: true,
    attribution: false,
    toolbarButtons: ['bold', 'italic', 'underline', 'paragraphFormat'],
    toolbarButtonsXS: ['bold', 'italic', 'underline', 'paragraphFormat'],
    toolbarButtonsSM: ['bold', 'italic', 'underline', 'paragraphFormat'],
    toolbarButtonsMD: ['bold', 'italic', 'underline', 'paragraphFormat'],
  };
  public afuConfig = {
    multiple: false,
    formatsAllowed: ".jpg,.png,.jpeg",
    maxSize: "50",
    uploadAPI: {
      url: global.url+'user/upload',
      method: "POST",
      headers: {
        "Authorization": this._userService.getToken()
      },
      responseType: 'json'
    },
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: true,
    hideSelectBtn: false,
    fileNameIndex: true,
    replaceTexts: {
      selectFileBtn: 'Select File',
      resetBtn: 'Reset',
      uploadBtn: 'Upload',
      attachPinBtn: 'Attach Files...',
      afterUploadMsg_success: 'Successfully Uploaded !',
      afterUploadMsg_error: 'Upload Failed !',
      sizeLimit: 'Size Limit'
    }
  };

  constructor(
    private _userService: UserService
  ) {
    this.page_title = 'User settings';
    this.user = new User(1, 'ROLE_USER', '', '', '', '', '', '');
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.url = global.url;

    // Fill user object
    this.user = new User(
      this.identity.sub,
      this.identity.role,
      this.identity.name,
      this.identity.surname,
      this.identity.email,
      '',
      this.identity.description,
      this.identity.image
    );

  }

  ngOnInit(): void {
  }

  onSubmit(_form) {
    this._userService.update(this.token, this.user).subscribe(
      response => {
        if (response && response.status) {

          this.status = 'success';
          // Update user in session
          if (response.changes.name) {
            this.user.name = response.changes.name;
          }
          if (response.changes.surname) {
            this.user.surname = response.changes.surname;
          }
          if (response.changes.description) {
            this.user.description = response.changes.description;
          }
          if (response.changes.image) {
            this.user.image = response.changes.image;
          }
          this.identity = response.token;

          localStorage.setItem('identity', JSON.stringify(this.identity));

        } else {
          this.status = 'error';
        }

      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    );

  }

  avatarUpload(data){
    let data_image = data.body.image;
    this.user.image = data_image;
    this.identity.image = data_image;
  }

}
