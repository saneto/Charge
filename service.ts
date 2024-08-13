// user-validation.service.ts
import { Injectable } from '@angular/core';
import { User } from './user.model';

@Injectable({
  providedIn: 'root',
})
export class UserValidationService {
  constructor() {}

  // Validate the 'name' field
  isValidName(name: string): boolean {
    return name.trim().length >= 3;
  }

  // Validate the 'email' field
  isValidEmail(email: string): boolean {
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return emailRegex.test(email);
  }

  // Validate the 'age' field
  isValidAge(age: number): boolean {
    return age >= 18;
  }

  // Validate the entire User object
  validateUser(user: User): { valid: boolean; errors: string[] } {
    const errors: string[] = [];

    if (!this.isValidName(user.name)) {
      errors.push('Name must be at least 3 characters long.');
    }

    if (!this.isValidEmail(user.email)) {
      errors.push('Invalid email format.');
    }

    if (!this.isValidAge(user.age)) {
      errors.push('Age must be 18 or older.');
    }

    const isValid = errors.length === 0;
    user.isValid = isValid; // Optionally set the isValid property

    return {
      valid: isValid,
      errors,
    };
  }
}