// user-guard.service.ts
import { Injectable } from '@angular/core';
import { UserValidationService } from './user-validation.service';
import { User } from './user.model';

@Injectable({
  providedIn: 'root',
})
export class UserGuardService {
  constructor(private userValidationService: UserValidationService) {}

  canProceed(user: User): boolean {
    const validationResult = this.userValidationService.validateUser(user);

    if (validationResult.valid) {
      return true; // Allow the process to continue
    } else {
      console.log('User validation failed:', validationResult.errors);
      return false; // Block the process
    }
  }
}