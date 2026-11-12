alter table doctors add column str_number VARCHAR(20);

CREATE unique index doctors_str_number_unique_idx
    on doctors (str_number)
    where str_number is not null;