// Faz um GET com axios.get e retorna a lista de usuários, response.data ja vem convertido de JSON

import axios from 'axios';

export async function getUsers(apiUrl) {
    try {
        const response = await axios.get(apiUrl);
        return response.data.users;
    } catch (error) {
        const message = error.response?.data?.error || 'Failed to load users';
        throw new Error(message);
    }
}