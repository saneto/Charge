// registration.component.ts
import { Component } from '@angular/core';
import { User } from './user.model';
import { UserGuardService } from './user-guard.service';

@Component({
  selector: 'app-registration',
  templateUrl: './registration.component.html',
})
export class RegistrationComponent {
  user: User = {
    name: '',
    email: '',
    age: null,
  };

  constructor(private userGuardService: UserGuardService) {}

  onSubmit() {
    if (this.userGuardService.canProceed(this.user)) {
      // Proceed with form submission, API calls, etc.
      console.log('User is valid. Proceeding with registration.');
    } else {
      // Block the process, inform the user
      console.log('User is not valid. Cannot proceed.');
    }
  }
}