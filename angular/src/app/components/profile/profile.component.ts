import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { Post } from '../../models/post';
import { PostService } from '../../services/post.service';
import { UserService } from 'src/app/services/user.service';
import { global } from '../../services/global';
import { User } from 'src/app/models/user';

@Component({
  selector: 'app-profile',
  templateUrl: './profile.component.html',
  styleUrls: ['./profile.component.css'],
  providers: [PostService, UserService],
})
export class ProfileComponent implements OnInit {
  public url;
  public status;
  public posts: Array<Post>;
  public identity;
  public token;
  public user: User;

  constructor(
    private _postService: PostService,
    private _userService: UserService,
    private _route: ActivatedRoute,
    private _router: Router
  ) {
    this.url = global.url;
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
  }

  ngOnInit(): void {
    this.getProfile();
  }

  getUser(id) {
    this._userService.getUser(id).subscribe(
      (response) => {
        if (response.status == 'success') {
          this.user = response.user;
          console.log(this.user);
        } else {
          this.status = 'error';
        }
      },
      (error) => {
        console.log(<any>error);
      }
    );
  }

  getPosts(id) {
    this._userService.getPosts(id).subscribe(
      (response) => {
        if (response.status == 'success') {
          this.posts = response.posts;
        } else {
          this.status = 'error';
        }
      },
      (error) => {
        console.log(<any>error);
      }
    );
  }

  getProfile() {
    this._route.params.subscribe((params) => {
      let id = +params['id'];

      this.getPosts(id);
      this.getUser(id);
    });
  }

  deletePost(id) {
    this._postService.delete(this.token, id).subscribe(
      (response) => {
        this.getProfile();
      },
      (error) => {
        console.log(<any>error);
      }
    );
  }
}
