import axios from 'axios';

export interface LoginCredentials {
    email: string;
    password: string;
}

export interface RegisterData {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
}

export interface AuthResponse {
    user: {
        id: number;
        name: string;
        email: string;
        user_type: 'super_admin' | 'admin' | 'partner' | 'mcc' | 'user';
        milk_collection_center_id?: number;
        partner_id?: number;
        is_active: boolean;
        milk_collection_center?: any;
        partner?: any;
        agent?: any;
    };
    token: string;
}

class AuthService {
    private baseURL = '';

    async login(credentials: LoginCredentials): Promise<AuthResponse> {
        const response = await axios.post<AuthResponse>(`${this.baseURL}/auth/login`, credentials);
        this.setAuthToken(response.data.token);
        this.setUser(response.data.user);
        return response.data;
    }

    async register(data: RegisterData): Promise<AuthResponse> {
        const response = await axios.post<AuthResponse>(`${this.baseURL}/auth/register`, data);
        this.setAuthToken(response.data.token);
        this.setUser(response.data.user);
        return response.data;
    }

    async logout(): Promise<void> {
        try {
            await axios.post(`${this.baseURL}/auth/logout`);
        } catch (error) {
            console.error('Logout error:', error);
        } finally {
            this.clearAuth();
        }
    }

    async getUser(): Promise<AuthResponse['user'] | null> {
        try {
            const response = await axios.get<AuthResponse['user']>(`${this.baseURL}/auth/user`);
            const userData = response.data;
            this.setUser(userData);
            return userData;
        } catch (error) {
            this.clearAuth();
            return null;
        }
    }

    setAuthToken(token: string): void {
        localStorage.setItem('auth_token', token);
        axios.defaults.headers.common['Authorization'] = `Bearer ${token}`;
    }

    getAuthToken(): string | null {
        return localStorage.getItem('auth_token');
    }

    setUser(user: AuthResponse['user']): void {
        localStorage.setItem('auth_user', JSON.stringify(user));
    }

    getUserFromStorage(): AuthResponse['user'] | null {
        const userStr = localStorage.getItem('auth_user');
        return userStr ? JSON.parse(userStr) : null;
    }

    clearAuth(): void {
        localStorage.removeItem('auth_token');
        localStorage.removeItem('auth_user');
        delete axios.defaults.headers.common['Authorization'];
    }

    isAuthenticated(): boolean {
        return !!this.getAuthToken();
    }
}

export default new AuthService();

