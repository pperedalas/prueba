import { Component, OnInit } from '@angular/core';
import { Router, ActivatedRoute, Params } from '@angular/router';
import { UserService } from '../../services/user.service';
import { CategoryService } from '../../services/category.service';
import { Category } from '../../models/category';

@Component({
  selector: 'app-category-new',
  templateUrl: './category-new.component.html',
  styleUrls: ['./category-new.component.css'],
  providers: [UserService, CategoryService]
})
export class CategoryNewComponent implements OnInit {
  public page_title: string;
  public token;
  public identity;
  public category;
  public status;

  constructor(
    private _userService: UserService,
    private _categoryService: CategoryService,
    private _router: Router,
    private _route: ActivatedRoute
  ) {
    this.page_title = "New category";
    this.identity = this._userService.getIdentity();
    this.token = this._userService.getToken();
    this.category = new Category(1, '');
    this.status = '';
  }

  ngOnInit(): void {
  }

  onSubmit(_form) {
    this._categoryService.create(this.token, this.category).subscribe(
      response => {
        if(response.status == 'success'){
          this.category = response.category;
          this.status = 'success';

          this._router.navigate(['/index'])
        }else{
          this.status = 'error';
        }
      },
      error => {
        this.status = 'error';
        console.log(<any>error);
      }
    )

  }

}
