<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Activity;

class ActivitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $activities = [
            // Wellness activities (activity_type_id: 1)
            [
                'name' => 'Sunrise Yoga at the Yoga Pavilion',
                'details' => 'Join our resident Yogi to start your day refreshed and full of energy at one of the complimentary daily sunrise yoga classes.',
                'activity_type_id' => 1
            ],
            [
                'name' => 'Crystal Dreaming at AVI Spa',
                'details' => 'After a day filled with adventure, treat yourself to a spa treatment full of wonder and crystal healing in our AVI Spa. After a holistic consultation indulge in Crystal healing haven.',
                'activity_type_id' => 1
            ],
            [
                'name' => '60 minutes Spa Treatment at AVI Spa',
                'details' => 'Dive deep into the world of holistic wellness, the way it is defined for your body and soul.',
                'activity_type_id' => 1
            ],
            
            // R&B activities (activity_type_id: 2)
            [
                'name' => 'Champagne Cocktail at the Sunset Bar',
                'details' => 'Sip on champagne cocktails crafted with infused iced spheres as you take in the sunset views at the helm of a Dhoni shaped bar. ',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Shisha Moments',
                'details' => 'With your feet in the sand and an endless panorama this is the perfect post-dinner hot spot. The mood is set, choose your shisha flavour and favourite sip to complement your night.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Private Picnic Lunch on the Sandbank',
                'details' => 'Enjoy the best of the Maldives, cast away on a private sandbank with light snacks and champagne. Just you and your chosen ones, explore the close by house reef before heading back to your island home.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Cooking Class',
                'details' => 'How about something local, something Asian topped-up with some super foods? Our Chef looks forward to cook home-made curry with you, create a salad full of goodness or something as delightful and simple as a perfect stone oven pizza.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Wine Journey',
                'details' => 'From champagne sabrage to the tasting and differentiating of a collection of white and red wines blindfolded, your journey into the world of wine commences.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Floating Breakfast in your Villa',
                'details' => "The likely most picturesque and private breakfast experience to opt-in for. Enjoy a sumptuous breakfast in your villa's private pool for an energetic start to your day.",
                'activity_type_id' => 2
            ],
            [
                'name' => 'Breakfast at Café Umi',
                'details' => 'Start the day with a hearty breakfast indulging in traditional favourites alongside authentic Asian selections, cooked-to-order dishes or mouthwatering pastries as you take in the majestic morning views.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Afternoon tea at the Retreat',
                'details' => 'Enjoy a delectable selection of afternoon tea treats right by the pool.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Afternoon tea at the Collective',
                'details' => 'Enjoy a delectable selection of afternoon tea treats right by the pool.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Aperitif hours at the Retreat ',
                'details' => 'Laze by the pool with a curated cocktail to cool you down and end the day with sundowners or evening digestives, premium wines and cheese.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Aperitif hours at the Pool Bar ',
                'details' => 'Laze by the pool with a curated cocktail to cool you down and end the day with sundowners or evening digestives, premium wines and cheese.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Lunch at The Retreat',
                'details' => 'Energising and healthy lunch options at The Retreat in combination with the mesmerizing views, light breeze and sounds of the waves.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'The Collective',
                'details' => 'The Collective offers picturesque views of the beach and the ocean and houses, a "Pizzeria" serving wood fired pizza.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Lunch at Café Umi',
                'details' => 'The perfect setting for a light or savoury lunch at Café Umi. Top up your lunch with fresh coconut water or your favourite  detox brew on the side.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Dinner at The Lighthouse ',
                'details' => 'Begin the evening on top of The Lighthouse with a scenic 360 degree view of the Indian Ocean. In the restaurant you sit down to vibrant flavours, crafted from the finest seasonal ingredients and seafood; where each dish is brought to life by the passion and expertise of our culinary team.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Wine Degustation at The Lighthouse',
                'details' => 'A dining experience, inspired by the Mediterranean coastline from France, Spain, Italy and Greece paired with fine wines and Champagne, to enjoy an evening of splendour and culinary indulgence.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Lobster at the Fish Market',
                'details' => 'Blending the freshest Maldivian seafood with aromatic flavours from South East Asia, Fish Market is a unique sea-to-table, interactive kitchen concept. ',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Dinner at the Fish Market',
                'details' => 'Blending the freshest Maldivian seafood with aromatic flavours from South East Asia, Fish Market is a unique sea-to-table, interactive kitchen concept.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Private Beach Dinner',
                'details' => 'With the help of your personal Island Curator, create your own private dining experience at the beach. A private chef and waiter at hand to pamper your culinary dreams.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Teppanyaki Dinner',
                'details' => 'A truly special dining experience at the Teppanyaki at Café Umi. Our Teppanyaki chef will serve your dinner in classic, Japanese Teppanyaki style.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Private Dinner on The Lighthouse Roof Top',
                'details' => 'Unobstructed view guaranteed. Your personal chef and waiter curate a picture-perfect evening for you, starting off with sunset drinks at the Lighthouse top, followed by a blissful dinner with exquisite cuisine.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Dinner at Café Umi',
                'details' => 'The relaxed ambience of Café Umi is the perfect place to enjoy the extensive menu on offer for dinner.',
                'activity_type_id' => 2
            ],
            [
                'name' => 'Dinner Under the Stars at The Lighthouse Beach ',
                'details' => "Private dining experience at the beach, under the stars. A private chef and waiter at hand to pamper your culinary dreams. Perfect moment of relaxation and indulgence, a quintessential beachside barbecue experience.",
                'activity_type_id' => 2
            ],
            
            // Activities (activity_type_id: 3)
            [
                'name' => 'Guided House Reef Snorkelling',
                'details' => 'Meet our Ocean Team at the Marine Centre to fit your snorkelling gear and head out with your guide to explore the magical underwater world of Maamunagau.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Private Luxury Yacht Cruise',
                'details' => 'Embark on your private cruise aboard our luxury yacht and curate your own adventure with snorkelling, diving or picnic on a deserted island, your imagination is our limit.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Luxury Yacht Cruise',
                'details' => 'Embark on your private cruise aboard our luxury yacht, find the leaping dolphins and chase the famous sunset of the Maldives, while sipping champagne and enjoying delectable canapes.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Manta Excursion',
                'details' => 'You may see one or you may see hundreds, magic is guaranteed if you meet those gentle giants on our snorkelling trip.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Turtle Safari',
                'details' => 'Luckily our guides know the area well, they will always find those mysterious and so beautiful underwater creature to swim with.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Water Sports',
                'details' => 'Visit our Marine Centre to find out all about our action packed water sports on offer. Jetski and Jetblades, Seabob and Wakeboarding and much more.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Professional Photographer',
                'details' => 'Capture these unforgettable moments in a once in a lifetime panorama with our in-house photographer.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'The Manta Rays of the Maldives Talk at The Retreat',
                'details' => 'Delve into the world of gentle giants, as the Manta Trust team introduces you to the life and biology of manta rays in the Maldives. Listen to their wonderful stories and learn about our efforts to protect them and their ocean home.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Guided Night Snorkelling',
                'details' => 'Experience our vivid house reef in a different light and discover what comes to life after dark.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Sunset Fishing',
                'details' => 'Bring on your fishing game and try your hand in fishing, a wonderful downtime for the whole family.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Adventure Jet Ski',
                'details' => 'A great way to explore the beautiful Indian ocean surrounding the resort. Skip across sapphire blue lagoons as you weave your way between the lush green islands that make up this breathtaking tropical landscape. Maybe if you’re lucky you’ll spot Manta rays or flying fish on this very trip.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Parasailing',
                'details' => 'Imagine taking off from the parasailing boat for an exhilarating flight up to an impressive 750 feet. Fly solo or with a loved one and enjoy the stunning panoramic view of the Indian ocean. For a truly unforgettable experience, parasail during sunset and soak up the warmth and glow of the remaining sunrays of the day. You may even spot schools of dolphins when they come out and play',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Dolphin Cruise',
                'details' => 'Our crew takes you out to search for Dolphins – a true spectacle of Dolphins flipping and spinning alongside the board while the sun sets on the horizon.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Meet and Greet the Manta Trust team',
                'details' => 'Meet the Manta Trust team at their base to discuss the coming days and planned activities.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'House Reef Dive (Refresher TBA)',
                'details' => 'Meet at the Marine Centre to get ready for your morning dive, before you head out to the house reef with guide.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Manta Research Trip',
                'details' => 'Manta Research at first hand. Join the Manta Trust team as they head out to collect their data, learn how to collect valid research data and why this is all so important to get it right.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Plankton Workshop in the Marine Centre',
                'details' => 'What was Plankton again? The foundation of life in our oceans and utterly interesting to have a look at them a bit closer.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Manta ID Research Workshop',
                'details' => 'Embark on your first Manta quest in Raa Atoll with the Manta Trust team. Today we are not just going to look for Mantas but also deploy RUVs and collect IDs of our friends. The team will take you through the art of identifying Manta Rays. Understanding their journey is the foundation of our research and protection efforts.',
                'activity_type_id' => 3
            ],
            [
                'name' => 'Snorkelling Excursion ',
                'details' => 'Embark on an exciting marine adventure, exploring the waters surrounding us. Our team will help you to look out for our favourites if they are around, like Manta Rays or Turtles and Sharks!',
                'activity_type_id' => 3
            ],
            
            // Kids activities (activity_type_id: 4)
            [
                'name' => 'Canvas Painting',
                'details' => 'Canvas painting for kids is an excellent activity that fosters creative exploration, confidence building, develops cognitive skills, and most importantly, it is a chance for them to discover themselves. ',
                'activity_type_id' => 4
            ],
            [
                'name' => 'Paint Your T-shirt',
                'details' => 'Always was dreaming of your own T-shirt Print? Join our Class and you will get chance to decorate your first DIY T-shirt which will express your personality and artistic skills. ',
                'activity_type_id' => 4
            ],
            [
                'name' => 'Sunset Catchers',
                'details' => 'Our Sunset Bar gives you access to the best seats in the house for our famous sunsets with the added bonus of a delicious drink.',
                'activity_type_id' => 4
            ],
            [
                'name' => 'Cookies Creation',
                'details' => 'Channel your inner master baker and join our pastry chef crew to create the cookies of your dreams!',
                'activity_type_id' => 4
            ],
            [
                'name' => 'Cupcake Artist',
                'details' => 'Chef hats on and off you go - its cupcake making time.',
                'activity_type_id' => 4
            ],
            
            // Sales activities (activity_type_id: 5)
            [
                'name' => 'Site Inspection with XXX',
                'details' => ' ',
                'activity_type_id' => 5
            ],
            [
                'name' => 'Sunset Drinks with XXX at Sunser Bar',
                'details' => 'Our Sunset Bar gives you access to the best seats in the house for our famous sunsets with the added bonus of a delicious drink. ',
                'activity_type_id' => 5
            ],
            [
                'name' => ' Dinner at XXX with XXX',
                'details' => ' A live Teppanyaki dinner to end the night with our incredibly talented Chef. Watch in awe as he works his magic and cooks up divine courses, one after the other, tantalising your tastebuds. ',
                'activity_type_id' => 5
            ],
            
            // Other activities (activity_type_id: 6)
            [
                'name' => 'Movie Night',
                'details' => 'Outdoor movie screening',
                'activity_type_id' => 6
            ],
            
            // Afternoon/Aperif activities (activity_type_id: 7)
            [
                'name' => 'Sunset Cocktails',
                'details' => 'Aperitifs and snacks during sunset',
                'activity_type_id' => 7
            ],
            
            // Lunch activities (activity_type_id: 8)
            [
                'name' => 'Beach BBQ',
                'details' => 'Lunchtime barbecue on the beach',
                'activity_type_id' => 8
            ],
            
            // Dinner activities (activity_type_id: 9)
            [
                'name' => 'Gala Dinner',
                'details' => 'Formal dinner with entertainment',
                'activity_type_id' => 9
            ],
        ];

        foreach ($activities as $activity) {
            Activity::create($activity);
        }
    }
}
