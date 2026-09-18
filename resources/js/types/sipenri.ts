export type Region = {
    id: number;
    name: string;
    code: string;
};

export type Student = {
    id: number;
    nis: string;
    name: string;
    dormitory: string;
    location_type: string;
    regions: Region[];
};

export type ShipmentStatus = 'created' | 'in_transit' | 'delivered';

export type RecentShipment = {
    id: number;
    tracking_code: string;
    status: ShipmentStatus;
    status_label: string;
    student_name: string;
    student_nis: string;
    package_count: number;
    amount_money: number;
    region: string | null;
    created_at: string;
};

export type Shipment = {
    id: number;
    tracking_code: string;
    status: ShipmentStatus;
    status_label: string;
    statuses?: Record<string, string>;
    sender_name: string;
    sender_phone: string | null;
    amount_money: number;
    package_count: number;
    location_type: string | null;
    created_at: string;
    updated_at: string;
    student: Student | null;
    region: Region | null;
    creator: { id: number; name: string } | null;
    logs?: ShipmentLog[];
};

export type ShipmentLog = {
    id: number;
    status_from: string | null;
    status_to: string;
    status_to_label: string;
    notes: string | null;
    attachment_url: string | null;
    logger: string | null;
    created_at: string;
};

export type User = {
    id: number;
    name: string;
    email: string;
    avatar?: string;
    role: 'super_admin' | 'iksass_admin';
    role_label?: string;
    region_iksass_id: number | null;
    region?: Region | null;
    region_name?: string | null;
    region_code?: string | null;
    email_verified_at?: string | null;
    two_factor_enabled?: boolean;
    created_at?: string;
    updated_at?: string;
};
