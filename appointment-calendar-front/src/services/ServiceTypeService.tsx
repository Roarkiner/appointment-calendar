import { api } from "./ApiService";

export const serviceTypePath = "/api/service-type";

export async function getAllServiceTypes(): Promise<ServiceType[]> {
    const response = await api.get<ServiceType[]>(serviceTypePath);
    
    const serviceTypes = response.data.map(serviceType => ({
        ...serviceType,
    }));
      
    return serviceTypes;
}