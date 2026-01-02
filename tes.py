import tkinter as tk
from tkinter import messagebox
from datetime import datetime

data_warga = []

# VALIDASI DATA
def validasi_data(nik, nama, jk):
    if nik == "" or nama == "" or jk == "":
        return False
    if not nik.isdigit() or len(nik) != 16:
        return False
    return True

# TAMBAH DATA
def tambah_warga():
    nik = entry_nik.get()
    nama = entry_nama.get()
    jk = var_jk.get()

    if not validasi_data(nik, nama, jk):
        messagebox.showerror(
            "Error",
            "Data tidak valid!\nPastikan NIK 16 digit dan semua field terisi."
        )
        return

    tanggal = datetime.now().strftime("%Y-%m-%d %H:%M:%S")
    warga = {
        "nik": nik,
        "nama": nama,
        "jenis_kelamin": jk,
        "tanggal_input": tanggal
    }
    data_warga.append(warga)

    entry_nik.delete(0, tk.END)
    entry_nama.delete(0, tk.END)
    var_jk.set("Pilih")

    messagebox.showinfo("Sukses", "Data warga berhasil ditambahkan")

# TAMPIL DATA 
def tampilkan_warga():
    text_output.delete(1.0, tk.END)

    if len(data_warga) == 0:
        text_output.insert(tk.END, "Data warga masih kosong")
        return

    text_output.insert(tk.END, "DAFTAR DATA WARGA\n")
    text_output.insert(tk.END, "-" * 55 + "\n")

    for i, w in enumerate(data_warga, start=1):
        text_output.insert(
            tk.END,
            f"{i}. {w['nik']} | {w['nama']} | {w['jenis_kelamin']} | {w['tanggal_input']}\n"
        )

# REKAP 
def rekap_warga():
    messagebox.showinfo(
        "Rekap Data",
        f"Total warga terdata: {len(data_warga)} orang"
    )

# HAPUS DATA BERDASARKAN NOMOR
def hapus_warga():
    nomor = entry_hapus.get()

    if not nomor.isdigit():
        messagebox.showerror("Error", "Masukkan nomor data yang valid")
        return

    idx = int(nomor) - 1

    if idx < 0 or idx >= len(data_warga):
        messagebox.showerror("Error", "Nomor data tidak ditemukan")
        return

    data_warga.pop(idx)
    entry_hapus.delete(0, tk.END)
    tampilkan_warga()
    messagebox.showinfo("Sukses", "Data warga berhasil dihapus")

# GUI 
root = tk.Tk()
root.title("Administrasi Data Warga")
root.geometry("650x520")

# FORM INPUT
frame_input = tk.Frame(root)
frame_input.pack(pady=10)

tk.Label(frame_input, text="NIK").grid(row=0, column=0, sticky="w")
entry_nik = tk.Entry(frame_input, width=30)
entry_nik.grid(row=0, column=1)

tk.Label(frame_input, text="Nama").grid(row=1, column=0, sticky="w")
entry_nama = tk.Entry(frame_input, width=30)
entry_nama.grid(row=1, column=1)

tk.Label(frame_input, text="Jenis Kelamin").grid(row=2, column=0, sticky="w")
var_jk = tk.StringVar(value="Pilih")
opsi_jk = tk.OptionMenu(frame_input, var_jk, "Laki-laki", "Perempuan")
opsi_jk.config(width=27)
opsi_jk.grid(row=2, column=1)

tk.Button(
    frame_input,
    text="Tambah Data",
    command=tambah_warga,
    width=20
).grid(row=3, column=0, columnspan=2, pady=8)

# MENU BUTTON
frame_menu = tk.Frame(root)
frame_menu.pack(pady=5)

tk.Button(frame_menu, text="Tampilkan Data", command=tampilkan_warga, width=25)\
    .grid(row=0, column=0, padx=5)

tk.Button(frame_menu, text="Rekap Data", command=rekap_warga, width=25)\
    .grid(row=0, column=1, padx=5)

# FORM HAPUS
frame_hapus = tk.Frame(root)
frame_hapus.pack(pady=5)

tk.Label(frame_hapus, text="Hapus Nomor").grid(row=0, column=0)
entry_hapus = tk.Entry(frame_hapus, width=10)
entry_hapus.grid(row=0, column=1, padx=5)

tk.Button(
    frame_hapus,
    text="Hapus Data",
    command=hapus_warga,
    width=20
).grid(row=0, column=2, padx=5)

# OUTPUT 
text_output = tk.Text(root, height=12, width=75)
text_output.pack(pady=10)

root.mainloop()
