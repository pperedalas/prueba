import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { CategoryService } from '../../services/category.service';
import { PostService } from '../../services/post.service';
import { Post } from '../../models/post';
import { global } from '../../services/global';

@Component({
  selector: 'app-post-new',
  templateUrl: './post-new.component.html',
  styleUrls: ['./post-new.component.css'],
  providers: [UserService, CategoryService, PostService]
})
export class PostNewComponent implements OnInit {
  public page_title: string;
  public identity;
  public token;
  public post: Post;
  public status;
  public url;
  public categories;

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
      url: global.url+'post/upload',
      method: "POST",
      headers: {
        "Authorization": this._userService.getToken()
      },
      responseType: 'json'
    },
    theme: "attachPin",
    hideProgressBar: false,
    hideResetBtn: false,
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
    private _userService: UserService,
    private _categoryService: CategoryService,
    private _postService: PostService,
    private _router: Router,
    private _route: ActivatedRoute
  ) {
    this.page_title = 'New post';
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.url = global.url;
  }

  ngOnInit(){
    this.post = new Post( 1, this.identity.sub, 1, '', '', null, null );
    this.getCategories();
  }

  getCategories(){
    this._categoryService.getAll().subscribe(
      response => {
        if ( response.status == 'success' ){
          this.categories = response.categories;
        } else {
          this.status = 'error';
        }
      },
      error => {
        console.log(<any>error);
      }
    )
  }

  onSubmit(_form){
    this._postService.create(this.token, this.post).subscribe(
      response => {
        if ( response.status == 'success' ){
          this.post = response.post;
          this.status = 'success';
          this._router.navigate(['/index']);
        } else {
          this.status = 'error';
        }
      },
      error => {
        console.log(<any>error);
      }
    );
  }

  imageUpload(data){
    let data_image = data.body.image;
    this.post.image = data_image;
  }

}
