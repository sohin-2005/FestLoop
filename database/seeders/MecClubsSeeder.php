<?php

namespace Database\Seeders;

use App\Models\Achievement;
use App\Models\Announcement;
use App\Models\Club;
use App\Models\Coordinator;
use App\Models\Event;
use App\Models\RoadmapItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * The real student clubs of Govt. Model Engineering College, Thrikkakara.
 *
 * Source: the club list at mec.ac.in/students/clubs plus each club's own
 * website where one exists (checked September 2026). Only publicly listed
 * club-level facts are included — no office bearers or personal contacts.
 * Descriptions are paraphrased, not copied.
 */
class MecClubsSeeder extends Seeder
{
    public function run(): void
    {
        foreach ($this->clubs() as $data) {
            $extras = [
                'achievements'  => $data['achievements'] ?? [],
                'roadmap'       => $data['roadmap'] ?? [],
                'announcements' => $data['announcements'] ?? [],
                'events'        => $data['events'] ?? [],
            ];
            unset($data['achievements'], $data['roadmap'], $data['announcements'], $data['events']);

            if ($logo = $this->storeLogo($data['short_name'])) {
                $data['logo_path'] = $logo;
            }

            $club = Club::updateOrCreate(
                ['name' => $data['name']],
                $data + ['status' => 'approved', 'approved_at' => now()]
            );

            // One placeholder coordinator login per club, so each can be handed over.
            $coordinator = Coordinator::updateOrCreate(
                ['email' => Str::slug($club->short_name).'@festloop.test'],
                ['name' => $club->name.' Coordinator', 'password' => Hash::make('password'), 'club_id' => $club->id, 'position' => 'Coordinator']
            );

            foreach ($extras['achievements'] as $a) {
                Achievement::updateOrCreate(['club_id' => $club->id, 'title' => $a['title']], $a);
            }

            foreach ($extras['roadmap'] as $i => $r) {
                RoadmapItem::updateOrCreate(['club_id' => $club->id, 'title' => $r['title']], $r + ['sort_order' => $i + 1]);
            }

            foreach ($extras['announcements'] as $n) {
                Announcement::updateOrCreate(['club_id' => $club->id, 'title' => $n['title']], $n);
            }

            foreach ($extras['events'] as $e) {
                $banner = $e['banner'] ?? null;
                unset($e['banner']);

                if ($banner && $stored = $this->storeEventBanner($banner)) {
                    $e['banner_image'] = $stored;
                }

                Event::updateOrCreate(
                    ['club_id' => $club->id, 'name' => $e['name']],
                    $e + ['coordinator_id' => $coordinator->id]
                );
            }
        }
    }

    /**
     * Club logos as published on mec.ac.in, shipped with the repo and copied
     * onto the public disk at seed time. Returns the public-disk path.
     */
    private function storeLogo(string $shortName): ?string
    {
        $file = [
            'MACS' => 'macs.png',       'BMA'  => 'bma.png',            'IEDC' => 'iedc.png',
            'IEEE' => 'ieee.png',       'IETE' => 'iete.png',           'MXS'  => 'mixed-signals.png',
            'ISTE' => 'iste.png',       'THUDI' => 'thudi.jpg',         'EMF'  => 'emf.png',
            '3EYE' => 'third-eye.png',  'ILU'  => 'illuminati.png',     'BMS'  => 'bhoomithrasena.jpg',
            'THL'  => 'thanal.png',     'TRM'  => 'terminal.png',       'WIE'  => 'wie.png',
            'FOSS' => 'fossmec.jpg',    'DBT'  => 'debate-club.png',    'GRN'  => 'greens.png',
            'ASME' => 'asme.png',       'RBT'  => 'reboot.png',         'TREE' => 'tree.png',
        ][$shortName] ?? null;

        if (! $file) {
            return null; // Fortitude has no logo published
        }

        $source = database_path('seeders/assets/club-logos/'.$file);

        if (! is_file($source)) {
            return null;
        }

        $path = 'club-logos/'.$file;
        Storage::disk('public')->put($path, file_get_contents($source));

        return $path;
    }

    /**
     * Event posters published by the organising club, shipped with the repo.
     * Only designed posters are used here — no event photographs, which show
     * identifiable attendees.
     */
    private function storeEventBanner(string $file): ?string
    {
        $source = database_path('seeders/assets/event-banners/'.$file);

        if (! is_file($source)) {
            return null;
        }

        $path = 'event-banners/'.$file;
        Storage::disk('public')->put($path, file_get_contents($source));

        return $path;
    }

    private function clubs(): array
    {
        return [
            [
                'name' => 'MEC Association of Computer Students', 'short_name' => 'MACS', 'category' => 'technical',
                'accent_color' => '#2E8FD6',
                'tagline' => 'The Computer Science department association.',
                'about' => 'Run by students of the Department of Computer Science and Engineering, MACS builds interest in the subject beyond the syllabus. It introduces members to what is happening in the industry and to concepts the curriculum does not cover.',
                'mission' => 'Build aptitude for computer science and awareness of where the field is heading.',
            ],
            [
                'name' => 'Biomedical Association', 'short_name' => 'BMA', 'category' => 'technical',
                'accent_color' => '#D6455A',
                'tagline' => 'Helping and uplifting biomedical engineers.',
                'about' => 'The association for students of the Biomedical Engineering department. BMA runs conferences, workshops and talks on where engineering meets medicine, and publishes its own newsletter editions.',
                'mission' => 'Equip biomedical engineers to design practical solutions to real healthcare needs.',
                'website' => 'https://bma.mec.ac.in', 'instagram' => 'bma.mec',
                'linkedin' => 'https://www.linkedin.com/in/biomedical-association-bma-766660128/',
            ],
            [
                'name' => 'Innovation and Entrepreneurship Development Cell', 'short_name' => 'IEDC', 'category' => 'professional',
                'accent_color' => '#F2762E', 'founded_year' => 2009,
                'tagline' => 'Job creators, not job seekers.',
                'about' => 'Started in 2009 as the college Entrepreneurship Cell and relaunched as IEDC in 2015 under Kerala Startup Mission, which funds it. The cell works as a pre-incubator: it mentors student projects, offers financial help with prototypes, and connects founders with alumni and industry mentors.',
                'mission' => 'Help students turn ideas into scalable startups and grow an entrepreneurial culture at MEC.',
                'website' => 'https://iedcmec.in', 'email' => 'iedc@mec.ac.in',
                'linkedin' => 'https://in.linkedin.com/company/iedcmec',
                'achievements' => [
                    ['title' => 'Technopreneur — national-level symposium', 'description' => 'The cell\'s flagship annual symposium, which has run for more than a decade and hosted speakers including a former ISRO chairman and an Infosys co-founder.'],
                ],
            ],
            [
                'name' => 'IEEE MEC Student Branch', 'short_name' => 'IEEE', 'category' => 'professional',
                'accent_color' => '#1F5FA8',
                'tagline' => 'The campus branch of the world\'s largest technical professional body.',
                'about' => 'The IEEE Student Branch gives students a platform to grow technically and professionally through IEEE\'s global network, with society chapters and affinity groups operating under it.',
                'mission' => 'Advance technology and innovation by building students\' technical and professional skills.',
            ],
            [
                'name' => 'IETE Students\' Forum', 'short_name' => 'IETE', 'category' => 'professional',
                'accent_color' => '#0E7C86', 'founded_year' => 2019,
                'tagline' => 'Electronics, telecommunication and IT, beyond the classroom.',
                'about' => 'The MEC forum of the Institution of Electronics and Telecommunication Engineers, a national professional society. It runs quizzes, circuit design and simulation challenges, coding contests, hardware events, webinars and training sessions.',
                'mission' => 'Advance the science and technology of electronics, telecommunication and IT among students.',
                'website' => 'https://iete.mec.ac.in', 'email' => 'ietestudentsforum.mec2019@gmail.com',
            ],
            [
                'name' => 'Mixed Signals', 'short_name' => 'MXS', 'category' => 'technical',
                'accent_color' => '#7A3E9D',
                'tagline' => 'Different minds. One frequency.',
                'about' => 'The Electronics Association, run by students of the Electronics and Communication department. Mixed Signals organises workshops, expert talks and competitions, from PCB design and FPGAs to embedded boards, so students can learn by building.',
                'mission' => 'Give students a place to learn, experiment and innovate with current electronics technology.',
                'website' => 'https://mixedsignals.mec.ac.in', 'instagram' => 'mixedsignalsmec',
                'linkedin' => 'https://www.linkedin.com/company/mixed-signals-mec',
                'events' => [
                    [
                        'name' => 'Astronix — Electronics & Space Robotics Talk',
                        'description' => 'An expert talk on electronics and space robotics, with speakers from ISRO\'s Inertial Systems Unit.',
                        'category' => 'talk', 'mode' => 'offline', 'location' => 'Govt. Model Engineering College',
                        'start_time' => Carbon::parse('2026-09-15 10:00'), 'end_time' => Carbon::parse('2026-09-15 12:00'),
                        'external_registration_url' => 'https://mixedsignals.mec.ac.in',
                        'banner' => 'astronix.jpg',
                    ],
                ],
            ],
            [
                'name' => 'ISTE Student Chapter', 'short_name' => 'ISTE', 'category' => 'professional',
                'accent_color' => '#3C8D5A',
                'tagline' => 'Technical education, student development.',
                'about' => 'The MEC chapter of the Indian Society for Technical Education, a national non-profit professional society focused on developing both teachers and students in technical education.',
                'mission' => 'Support students\' personal and professional growth as part of India\'s technical education system.',
            ],
            [
                'name' => 'Thudi', 'short_name' => 'THUDI', 'category' => 'cultural',
                'accent_color' => '#B5651D',
                'tagline' => 'The nature-cum-literary club.',
                'about' => 'Named after a traditional drum once used in folk song and storytelling, Thudi brings together students from every class. It champions Malayalam: reading, writing and speaking it, and helping students who never had the chance to learn it.',
                'mission' => 'Keep the mother tongue and the region\'s cultural roots alive on campus.',
                'instagram' => 'thudi.mec',
            ],
            [
                'name' => 'Electrical Minds Forum', 'short_name' => 'EMF', 'category' => 'technical',
                'accent_color' => '#E0A800',
                'tagline' => 'Where the EEE department meets industry.',
                'about' => 'The student society of the Electrical and Electronics Engineering department, working to connect students with developments across the power and electronics industry.',
                'mission' => 'Bridge the gap between classroom learning and progress in the industry.',
            ],
            [
                'name' => 'Third Eye', 'short_name' => '3EYE', 'category' => 'arts',
                'accent_color' => '#171A3D',
                'tagline' => 'The photography club.',
                'about' => 'A community of students who share a passion for photography. Beyond helping new photographers develop and show their work, the club runs an organised team that covers every major event on campus.',
                'mission' => 'Nurture budding photographers and document campus life.',
            ],
            [
                'name' => 'Illuminati', 'short_name' => 'ILU', 'category' => 'cultural',
                'accent_color' => '#C2185B', 'founded_year' => 2004,
                'tagline' => 'The quizzing fraternity of MEC.',
                'about' => 'One of the most active student societies on campus. Illuminati has conducted well over a hundred intra- and inter-collegiate quizzes and runs The Illuminati Quiz, an annual South India-level contest started in 2008.',
                'mission' => 'Keep the college\'s quizzing tradition going, both on campus and in the wider quizzing community.',
                'website' => 'https://illuminati.mec.ac.in',
                'achievements' => [
                    ['title' => '100+ quizzes conducted', 'description' => 'Intra- and inter-collegiate quizzes organised by the club over the years.'],
                    ['title' => 'The Illuminati Quiz — 19 editions', 'description' => 'An annual collegiate quiz running since 2008, now one of the largest in South India.', 'achieved_on' => Carbon::parse('2008-12-08')],
                ],
                'roadmap' => [
                    ['title' => 'The Illuminati Quiz, 19th edition', 'status' => 'in_progress', 'target_label' => 'Oct 2026'],
                ],
                'events' => [
                    [
                        'name' => 'The Illuminati Quiz — 19th Edition',
                        'description' => "Illuminati's annual collegiate quiz returns for its 19th edition, bringing top quizzers together for a fast-paced contest with a cash prize pool of over ₹55,000.\n\nQuizmaster: Major Chandrakanth Nair. Check the official site for reporting time and team rules.",
                        'category' => 'competition', 'mode' => 'offline', 'location' => 'Bhavans Hall, TD Road, Kochi',
                        'start_time' => Carbon::parse('2026-10-02 09:30'),
                        'external_registration_url' => 'https://illuminati.mec.ac.in',
                        'banner' => 'tiq.png',
                    ],
                ],
            ],
            [
                'name' => 'Bhoomithrasena', 'short_name' => 'BMS', 'category' => 'social',
                'accent_color' => '#2E7D32',
                'tagline' => 'Environmental education and action.',
                'about' => 'An initiative of the state Directorate of Environment and Climate Change. The club carries out environmental protection and awareness activities around the college and encourages students to live and promote a sustainable lifestyle.',
                'mission' => 'Protect the environment around campus and make sustainable living a habit.',
            ],
            [
                'name' => 'THANAL', 'short_name' => 'THL', 'category' => 'social',
                'accent_color' => '#E65100',
                'tagline' => 'To Humans, A Noble Acme Life.',
                'about' => 'A student-run social action club working on humanitarian welfare, child and social empowerment. Thanal raises funds and supplies for people who need them, and encourages young professionals to make time for their responsibilities to society.',
                'mission' => 'Treat charity as a duty rather than a favour, and help students give back regularly.',
                'website' => 'https://thanal.mec.ac.in', 'email' => 'thanalmec.mec@gmail.com',
                'achievements' => [
                    ['title' => 'Pratheeksha dialysis support', 'description' => 'Monthly funds raised by the club go toward medication and treatment for dialysis patients who cannot afford them.'],
                    ['title' => 'Weekly food drive', 'description' => 'Around 100 food packets collected from students and distributed around Kakkanad every Friday.'],
                ],
                'announcements' => [
                    ['title' => 'Friday food packet collection', 'body' => 'Bring a food packet on Fridays. Thanal members distribute them to people in need around Kakkanad.', 'is_pinned' => true],
                    ['title' => 'One Day One Rupee', 'body' => 'Contribute one rupee a day through your class representative. It is collected monthly and funds all of Thanal\'s charity work.', 'is_pinned' => false],
                ],
            ],
            [
                'name' => 'Terminal', 'short_name' => 'TRM', 'category' => 'arts',
                'accent_color' => '#455A64',
                'tagline' => 'The official technical publication of MEC.',
                'about' => 'A student initiative run with the IEEE wing, Terminal is the college\'s technical publication, covering developments in technology both locally and around the world.',
                'mission' => 'Spark readers\' curiosity about the technology changing the world around them.',
            ],
            [
                'name' => 'IEEE Women in Engineering', 'short_name' => 'WIE', 'category' => 'professional',
                'accent_color' => '#8E24AA',
                'tagline' => 'Recruiting and retaining women in tech.',
                'about' => 'The IEEE WIE affinity group at MEC is part of a global community of IEEE members who use their diverse talents to innovate for the benefit of humanity.',
                'mission' => 'Help bring women into technical disciplines and keep them there.',
            ],
            [
                'name' => 'FOSS MEC', 'short_name' => 'FOSS', 'category' => 'technical',
                'accent_color' => '#43A047',
                'tagline' => 'The Free and Open Source Software community at MEC.',
                'about' => 'The Free and Open Source Software cell brings the college\'s open-source enthusiasts under one roof. Members learn by contributing to real projects, through expert talks, hands-on workshops and collaborative build sessions.',
                'mission' => 'Promote free and open source software and a culture of open collaboration among students.',
                'website' => 'https://foss.mec.ac.in', 'instagram' => 'foss_mec',
                'linkedin' => 'https://linkedin.com/company/fossmec',
            ],
            [
                'name' => 'Debate Club', 'short_name' => 'DBT', 'category' => 'cultural',
                'accent_color' => '#5D4037', 'founded_year' => 2014,
                'tagline' => 'For those who think out loud.',
                'about' => 'Started by students in 2014 for anyone with a flair for expressing ideas, in speech or in writing. The club holds weekly after-class sessions where members build confidence and find their voice.',
                'mission' => 'Give every student a platform to voice opinions and speak with confidence.',
            ],
            [
                'name' => 'Greens', 'short_name' => 'GRN', 'category' => 'technical',
                'accent_color' => '#558B2F',
                'tagline' => 'The Mechanical Engineering Association.',
                'about' => 'The association of the Mechanical Engineering department, founded on the responsible use, conservation and preservation of energy and natural resources.',
                'mission' => 'Encourage engineers to become green engineers who protect resources for future generations.',
            ],
            [
                'name' => 'Fortitude', 'short_name' => 'FRT', 'category' => 'social',
                'accent_color' => '#00838F',
                'tagline' => 'A safe space to talk about mental health.',
                'about' => 'Fortitude is building a dependable student network for anyone looking for information, clarity or support around mental health. It creates a safe, inclusive space on campus for conversations and activities about wellbeing.',
                'mission' => 'Make mental health support easy to find and easy to talk about at MEC.',
                'announcements' => [
                    ['title' => 'Counselling is available', 'body' => 'The college offers a counselling facility. A request form is linked from Fortitude\'s section on the MEC clubs page: https://www.mec.ac.in/students/clubs', 'is_pinned' => true],
                ],
            ],
            [
                'name' => 'ASME MEC', 'short_name' => 'ASME', 'category' => 'professional',
                'accent_color' => '#0D47A1',
                'tagline' => 'The ASME student chapter.',
                'about' => 'The student chapter of ASME, guided by faculty from the Centre for Energy Management Studies in the Mechanical Engineering department. It runs hands-on projects, technical events and participation in ASME competitions.',
                'mission' => 'Bridge the gap between classroom learning and what industry expects of engineers.',
            ],
            [
                'name' => 'Reboot', 'short_name' => 'RBT', 'category' => 'technical',
                'accent_color' => '#6D4C41',
                'tagline' => 'A general innovation club.',
                'about' => 'Reboot helps every member grow technically by focusing on innovation and the new research happening in technology today.',
                'mission' => 'Raise the technical calibre of every member through hands-on innovation.',
            ],
            [
                'name' => 'TREE', 'short_name' => 'TREE', 'category' => 'social',
                'accent_color' => '#33691E',
                'tagline' => 'The Road to Environmental Excellence.',
                'about' => 'A student body started by MEC\'s green lovers to promote and protect the vegetation in and around the campus.',
                'mission' => 'Keep the campus green and growing.',
            ],
        ];
    }
}
