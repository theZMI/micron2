import axios from 'axios';
import { IsApiResponse, ToApiResponse } from '@/helpers/api';
import EnvConfig from "@/env";
import store from "@/store";
import { UserModel } from "@/models/UserModel";

export default {
    API_ROOT_URL: `${EnvConfig.SITE_ROOT_URL}api/v1.0/`,
    CONF_TIMEOUT: 5000,
    lastResponse: {},

    _prepareResponse(response) {
        // If response in incorrect format, then show error
        if (!IsApiResponse(response)) {
            response = ToApiResponse(response);
            response.is_error = true;
        }
        // If both flags (is_success and is_error) set as false, then show error
        if (!response.is_success && !response.is_error) {
            response.is_error = true;
        }
        return response;
    },

    _needAuth() {
        UserModel.current().logout();
        window.location.reload();
    },

    async _call(uri, method, data) {
        store.commit('networkInformation/update', { status: 'loading' });
        const send_request_time = Math.floor(Date.now() / 1000);

        try { // Запрос выполнился успешно, и is_success==true
            const response = await axios({
                url: `${this.API_ROOT_URL}${uri}`,
                responseType: 'json',
                timeout: this.CONF_TIMEOUT,
                method,
                params: method === 'GET' ? data : [],
                data,
                headers: {
                    'Content-Type' : 'application/json; charset=UTF-8',
                    'API-Request-Time': send_request_time.toString(),
                    'API-User-Auth-Login': UserModel.current().login,
                    'API-User-Auth-Token': UserModel.current().token
                },
            });
            this.lastResponse = {
                ...this._prepareResponse(response.data),
                is_response_from_server: IsApiResponse(response.data),
                status: response.status,
                send_request_time,
                response_time: Math.floor(Date.now() / 1000),
            };

            if (this.lastResponse.is_success) {
                store.commit('networkInformation/update', { status: 'success' });
                return this.lastResponse.data;
            }
        } catch (e) { // Ошибка выполнения запроса
            this.lastResponse = {
                ...this._prepareResponse(e?.response?.data || 'Ошибка подключения к сети'),
                is_response_from_server: IsApiResponse(e?.response?.data || ''),
                status: e?.response?.status || 400,
                send_request_time,
                response_time: Math.floor(Date.now() / 1000),
                is_server_maintenance: e?.response?.status === 503,
                error: e,
            };
            store.commit(
                'networkInformation/update',
                {
                    status: this.lastResponse.is_response_from_server ? 'success' : 'error',
                    lastError: this.lastResponse?.error?.message
                }
            );
            if (e?.response?.status === 401) { // Ошибка авторизации
                this._needAuth();
            }

            throw this.lastResponse;
        }

        // Запрос успешно выполнился, но is_error==true
        store.commit(
            'networkInformation/update',
            {
                status: IsApiResponse(this.lastResponse) ? 'success' : 'error',
                lastError: this.lastResponse?.error?.message
            }
        );
        throw {
            ...this.lastResponse,
            is_response_from_server: IsApiResponse(this.lastResponse),
            status: 200,
            send_request_time,
        }
    },

    get(uri, data = []) {
        return this._call(uri, 'GET', data);
    },

    post(uri, data) { // Создание ресурса
        return this._call(uri, 'POST', data);
    },

    put(uri, data) { // Замена ресурса целиком
        return this._call(uri, 'PUT', data);
    },

    patch(uri, data) { // Редактирование ресурса
        return this._call(uri, 'PATCH', data);
    },

    delete(uri, data = []) {
        return this._call(uri, 'DELETE', data);
    }
}