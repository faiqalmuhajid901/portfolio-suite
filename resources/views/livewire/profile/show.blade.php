<div class="space-y-10">
    <section class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        <div class="rounded-[28px] bg-white p-8 shadow-sm lg:col-span-2">
            <p class="text-sm font-semibold uppercase tracking-[0.2em] text-[#2f6f61]">
                Professional Profile
            </p>

            <h1 class="mt-4 text-4xl font-bold">
                {{ auth()->user()->name ?? 'Alex Rivera' }}
            </h1>

            <p class="mt-4 max-w-2xl text-gray-600">
                Strategic portfolio manager with strong experience in digital product management,
                creative direction, and scalable project execution.
            </p>

            <div class="mt-8 flex flex-wrap gap-4">
                <button class="rounded-xl bg-[#7fac9f] px-6 py-3 text-sm font-semibold text-white">
                    Download Portfolio
                </button>

                <button class="rounded-xl border border-[#7fac9f] px-6 py-3 text-sm font-semibold text-[#2f6f61]">
                    Get in Touch
                </button>
            </div>
        </div>

        <div class="rounded-[28px] bg-[#2f6f61] p-8 text-white shadow-sm">
            <p class="text-sm text-white/70">Profile Score</p>
            <h2 class="mt-6 text-5xl font-bold">92%</h2>
            <p class="mt-3 text-white/80">Strong portfolio readiness</p>
        </div>
    </section>

<livewire:profile.bio-editor />

    <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="rounded-[28px] bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold">Technical Arsenal</h2>

            <p class="mt-2 text-sm text-gray-500">
                Skill distribution based on project experience.
            </p>

            <div class="mt-8 space-y-6">
                @foreach ($skills as $skill)
                    <div>
                        <div class="flex justify-between text-sm">
                            <span class="font-medium">{{ $skill->name }}</span>
                            <span class="text-gray-500">{{ $skill->percentage }}%</span>
                        </div>

                        <div class="mt-2 h-2 rounded-full bg-gray-200">
                            <div
                                class="h-2 rounded-full bg-[#2f6f61]"
                                style="width: {{ $skill->percentage }}%"
                            ></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="rounded-[28px] bg-white p-8 shadow-sm">
            <h2 class="text-2xl font-bold">Career Trajectory</h2>

            <div class="mt-8 space-y-5">
                @foreach ($careers as $career)
                    <div class="rounded-2xl border border-gray-100 p-5">
                        <p class="text-sm text-[#2f6f61]">{{ $career->period }}</p>
                        <h3 class="mt-2 font-semibold">{{ $career->title }}</h3>
                        <p class="text-sm text-gray-500">{{ $career->company }} · {{ $career->location }}</p>
                        <p class="mt-3 text-sm text-gray-600">{{ $career->description }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
</div>
