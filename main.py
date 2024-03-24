import tkinter as tk


def main():
    root = tk.Tk()
    root.title('Autonode')

    root.geometry("1024x768")

    label = tk.Label(root, text="Fu")
    label.pack(pady=20)

    root.mainloop()


if __name__ == "__main__":
    main()
