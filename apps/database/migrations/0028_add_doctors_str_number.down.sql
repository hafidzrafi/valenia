drop index if exists doctors_str_number_unique_idx;

alter table doctors drop column if exists str_number;