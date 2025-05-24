import { Navbar } from "@/components/Navbar/Navbar"

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <div className="min-h-screen flex flex-col bg-background">
      <Navbar />
      <main className="flex-1">
        <div className="container mx-auto max-w-5xl px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
          {children}
        </div>
      </main>
    </div>
  );
}
