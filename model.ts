// user.model.ts
export interface User {
  name: string;
  email: string;
  age: number;
  isValid?: boolean; // Optional property to mark validity
}