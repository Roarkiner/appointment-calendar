import { api } from "./ApiService";
import { formatDate } from "../services/DateHelper";

export const appointmentPath = "/api/appointment";

export async function getAllAppointmentsBetween(startDate: Date, endDate: Date): Promise<Appointment[]> {
    const response = await api.get<Appointment[]>(appointmentPath, {
        params: {
          start_date: formatDate(startDate),
          end_date: formatDate(endDate),
        },
    });
    
    const appointments = response.data.map(appointment => ({
        ...appointment,
        start_date: new Date(appointment.start_date),
        end_date: new Date(appointment.end_date),
    }));
      
    return appointments;
}