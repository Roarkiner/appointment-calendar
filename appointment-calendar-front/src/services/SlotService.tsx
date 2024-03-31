import { api } from "./ApiService";
import { formatDate } from "./DateHelper";

export const slotPath = "/api/slot";

export async function getAllSlotsBetween(startDate: Date, endDate: Date): Promise<Slot[]> {
    const response = await api.get<Slot[]>(slotPath, {
        params: {
          start_date: formatDate(startDate),
          end_date: formatDate(endDate),
        },
    });
    
    const slots = response.data.map(slot => ({
        ...slot,
        start_date: new Date(slot.start_date),
        end_date: new Date(slot.end_date),
    }));
      
    return slots;
}