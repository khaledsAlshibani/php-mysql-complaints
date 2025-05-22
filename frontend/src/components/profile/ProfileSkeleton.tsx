import { Card, CardContent, CardHeader } from "@/components/ui/card";
import { Skeleton } from "@/components/ui/skeleton";

export function ProfileSkeleton() {
    return (
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            <aside className="space-y-6">
                <Card>
                    <CardHeader className="space-y-6">
                        <div className="flex justify-center">
                            <Skeleton className="w-24 h-24 rounded-full" />
                        </div>
                        <div className="space-y-2">
                            <Skeleton className="h-4 w-[200px] mx-auto" />
                            <Skeleton className="h-4 w-[150px] mx-auto" />
                        </div>
                        <Skeleton className="h-5 w-[100px] mx-auto" />
                    </CardHeader>
                </Card>
                <Card>
                    <CardHeader>
                        <Skeleton className="h-5 w-[150px]" />
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <Skeleton className="h-4 w-full" />
                        <Skeleton className="h-4 w-full" />
                    </CardContent>
                </Card>
            </aside>
            <div className="md:col-span-2 space-y-6">
                <Card>
                    <CardHeader>
                        <Skeleton className="h-5 w-[200px]" />
                        <Skeleton className="h-4 w-[300px]" />
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                            {Array(4).fill(null).map((_, i) => (
                                <div key={i} className="space-y-2">
                                    <Skeleton className="h-4 w-[100px]" />
                                    <Skeleton className="h-4 w-[150px]" />
                                </div>
                            ))}
                        </div>
                    </CardContent>
                </Card>

                <Card>
                    <CardHeader>
                        <Skeleton className="h-5 w-[150px]" />
                        <Skeleton className="h-4 w-[250px]" />
                    </CardHeader>
                    <CardContent className="space-y-4">
                        <div className="flex gap-2">
                            <Skeleton className="h-10 w-[100px]" />
                            <Skeleton className="h-10 w-[120px]" />
                        </div>
                    </CardContent>
                </Card>
            </div>
        </div>
    );
}
