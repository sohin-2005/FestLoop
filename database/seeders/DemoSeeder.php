<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Club;
use App\Models\Coordinator;
use App\Models\Event;
use App\Models\Registration;
use App\Models\RoadmapItem;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Fictional clubs, events and students for demos and UI work.
 * Not run by default: php artisan db:seed --class=DemoSeeder
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        $students = User::factory(24)->create(['role' => 'student']);

        $clubs = [
            [
                'name' => 'ByteForge Coding Club', 'short_name' => 'BFC', 'category' => 'technical',
                'tagline' => 'Ship weird stuff. Ship it fast.',
                'accent_color' => '#F2762E', 'founded_year' => 2018,
                'about' => "ByteForge is where the campus's hackers, builders, and open-source tinkerers hang out. We run weekly build nights, a fall hackathon, and a mentorship track for first-years learning to code.",
                'mission' => 'Make “I built that” the most common sentence on campus.',
            ],
            [
                'name' => 'Kaleidoscope Dance Crew', 'short_name' => 'KDC', 'category' => 'cultural',
                'tagline' => 'Every rhythm has a home here.',
                'accent_color' => '#7A3E9D', 'founded_year' => 2015,
                'about' => 'From hip-hop to classical fusion, Kaleidoscope trains and performs across styles. We hold open practice every Tuesday and represent the college at inter-collegiate fests.',
                'mission' => 'Grow dancers of every level into a crew that performs without fear.',
            ],
            [
                'name' => 'Lens Collective', 'short_name' => 'LC', 'category' => 'arts',
                'tagline' => 'Photography, film, and the stories in between.',
                'accent_color' => '#2E8FD6', 'founded_year' => 2020,
                'about' => 'We run photowalks, a monthly short-film night, and cover every major campus event. Bring a phone or a DSLR — both are welcome.',
                'mission' => 'Document campus life the way it actually feels, not just how it looks.',
            ],
            [
                'name' => 'Sportsfolio Athletics Council', 'short_name' => 'SAC', 'category' => 'sports',
                'tagline' => 'Train together, play harder.',
                'accent_color' => '#3C8D5A', 'founded_year' => 2012,
                'about' => 'We organize intramural leagues in football, basketball, and badminton, plus open gym sessions and the annual Sports Week.',
                'mission' => 'A team for every student, regardless of skill level.',
            ],
            [
                'name' => 'GreenLoop Sustainability Cell', 'short_name' => 'GSC', 'category' => 'social',
                'tagline' => 'Small habits, campus-wide impact.',
                'accent_color' => '#3C8D5A', 'founded_year' => 2021,
                'about' => 'We run the campus composting program, tree plantation drives, and a thrift-and-swap market every semester.',
                'mission' => 'Make sustainable choices the easy choices on campus.',
            ],
            [
                'name' => 'Founders Circle', 'short_name' => 'FC', 'category' => 'professional',
                'tagline' => 'Where the next campus startup starts.',
                'accent_color' => '#F2762E', 'founded_year' => 2019,
                'about' => 'A community for students building startups — pitch nights, founder AMAs, and a small pre-seed fund for student ventures.',
                'mission' => 'Turn one dorm-room idea a year into a real company.',
            ],
        ];

        $eventTemplates = [
            'technical'   => ['HackNight: 12hr Build Sprint', 'Intro to Git & GitHub', 'AI/ML Study Jam', 'CTF: Capture the Flag'],
            'cultural'    => ['Open Practice Session', 'Fusion Night: Auditions', 'Inter-College Showcase'],
            'arts'        => ['Golden Hour Photowalk', 'Short Film Screening Night', 'Portrait Lighting Workshop'],
            'sports'      => ['5-a-Side Football League', 'Badminton Open Doubles', 'Sports Week Kickoff'],
            'social'      => ['Campus Swap Market', 'Tree Plantation Drive', 'Compost 101 Workshop'],
            'professional'=> ['Pitch Night Vol. 3', 'Founder AMA: Seed to Series A', 'Resume & LinkedIn Clinic'],
        ];

        $categoryMap = [
            'technical' => 'technical', 'cultural' => 'cultural', 'arts' => 'cultural',
            'sports' => 'sports', 'social' => 'social', 'professional' => 'talk',
        ];

        foreach ($clubs as $i => $clubData) {
            $club = Club::create($clubData + ['status' => 'approved', 'approved_at' => now()->subDays(rand(30, 300))]);

            $coordinator = Coordinator::create([
                'name'     => fake()->name(),
                'email'    => strtolower($club->short_name).'@festloop.test',
                'password' => Hash::make('password'),
                'club_id'  => $club->id,
                'position' => 'President',
            ]);

            // Followers
            $club->followers()->attach($students->random(rand(4, 14))->pluck('id'));

            // Achievements
            Achievement::create([
                'club_id' => $club->id,
                'title' => 'Best Club Award '.now()->subYear()->format('Y'),
                'description' => 'Recognized by the Dean of Student Affairs for outstanding campus engagement.',
                'achieved_on' => now()->subMonths(rand(3, 10)),
            ]);
            Achievement::create([
                'club_id' => $club->id,
                'title' => '1st Place — Inter-College Meet',
                'description' => 'Our team took the top spot against 12 other colleges.',
                'achieved_on' => now()->subMonths(rand(1, 6)),
            ]);

            // Roadmap
            RoadmapItem::create(['club_id' => $club->id, 'title' => 'Grow active membership to 150+', 'status' => 'in_progress', 'target_label' => 'This semester', 'sort_order' => 1]);
            RoadmapItem::create(['club_id' => $club->id, 'title' => 'Launch a beginner-friendly onboarding track', 'status' => 'planned', 'target_label' => 'Next semester', 'sort_order' => 2]);
            RoadmapItem::create(['club_id' => $club->id, 'title' => 'Host our first city-wide open event', 'status' => 'planned', 'target_label' => '2027', 'sort_order' => 3]);

            // Announcement
            Announcement::create([
                'club_id' => $club->id,
                'title' => 'Recruitment is open!',
                'body' => "We're onboarding new members all month — no experience needed, just show up to any open session.",
                'is_pinned' => true,
            ]);

            $templates = $eventTemplates[$clubData['category']];
            $eventCategory = $categoryMap[$clubData['category']];

            foreach ($templates as $j => $name) {
                $daysOffset = ($j - 1) * 14 + rand(-3, 3); // mix of past / near / future
                $start = now()->addDays($daysOffset)->setTime(rand(9, 18), [0, 15, 30, 45][array_rand([0,15,30,45])]);

                $event = Event::create([
                    'name' => $name,
                    'description' => fake()->paragraphs(3, true),
                    'location' => fake()->randomElement(['Main Auditorium', 'Seminar Hall B', 'Sports Complex', 'Innovation Lab', 'Open Air Theatre', 'Room 204, Block C']),
                    'start_time' => $start,
                    'end_time' => $start->copy()->addHours(rand(1, 4)),
                    'category' => $eventCategory,
                    'mode' => fake()->randomElement(['offline', 'offline', 'offline', 'hybrid']),
                    'venue_details' => fake()->sentence(12),
                    'max_participants' => fake()->randomElement([null, 30, 50, 80, 100]),
                    'registration_deadline' => $start->copy()->subDay(),
                    'requires_approval' => fake()->boolean(20),
                    'contact_email' => $coordinator->email,
                    'rules' => fake()->boolean(50) ? fake()->paragraph() : null,
                    'coordinator_id' => $coordinator->id,
                    'club_id' => $club->id,
                    'recap' => $daysOffset < 0 ? fake()->paragraph() : null,
                ]);

                // Registrations for a realistic mix
                $registrants = $students->random(min($students->count(), rand(3, 18)));
                foreach ($registrants as $s) {
                    Registration::firstOrCreate([
                        'user_id' => $s->id,
                        'event_id' => $event->id,
                    ], [
                        'status' => fake()->randomElement(['registered', 'registered', 'registered', 'pending', 'waitlisted']),
                    ]);
                }
            }
        }

        // A pending club awaiting admin approval, to demo the admin flow.
        $pendingClub = Club::create([
            'name' => 'Chessmates Club', 'category' => 'social', 'tagline' => 'Sixty-four squares, endless plans.',
            'about' => 'A new club for casual and competitive chess players alike.', 'status' => 'pending',
        ]);
        Coordinator::create([
            'name' => 'Priya Nair', 'email' => 'chessmates@festloop.test',
            'password' => Hash::make('password'), 'club_id' => $pendingClub->id, 'position' => 'Founder',
        ]);
    }
}
