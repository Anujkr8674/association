<?php
require_once 'config.php';
// Include the shared header
include 'includes/header.php';
?>

<style>
    /* ==========================================================================
       DURGA PUJA BACKGROUND SPECIFIC STYLES
       ========================================================================== */
    .bg-banner {
        background: linear-gradient(135deg, var(--red) 0%, #581010 100%);
        color: var(--white);
        padding: 9rem 0 5rem 0;
        text-align: center;
        position: relative;
        overflow: hidden;
    }

    .bg-banner::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-image: radial-gradient(circle at 20% 50%, rgba(201, 154, 46, 0.15) 0%, transparent 50%),
                          radial-gradient(circle at 80% 50%, rgba(200, 59, 45, 0.15) 0%, transparent 50%);
        z-index: 1;
    }

    .bg-banner-title {
        font-size: clamp(2.5rem, 5vw, 3.5rem);
        font-family: var(--font-headings);
        color: var(--white);
        margin-bottom: 1rem;
        position: relative;
        z-index: 2;
    }

    .bg-banner-subtitle {
        font-size: 1.1rem;
        color: var(--gold);
        font-weight: 500;
        letter-spacing: 1px;
        text-transform: uppercase;
        position: relative;
        z-index: 2;
    }

    .bg-sec {
        padding: 6.5rem 0;
        background-color: var(--cream);
    }

    .bg-container {
        max-width: 900px;
        margin: 0 auto;
        padding: 0 1.5rem;
    }

    .bg-content {
        background-color: var(--white);
        border: 1px solid var(--border-color);
        border-radius: var(--border-radius-lg);
        padding: 3.5rem;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
    }

    .bg-content:hover {
        background-color: var(--red);
        border-color: var(--gold);
        box-shadow: var(--shadow-lg);
    }

    .bg-content:hover h2 {
        color: var(--gold);
        border-bottom-color: rgba(255, 255, 255, 0.15);
    }

    .bg-content:hover p {
        color: rgba(255, 255, 255, 0.9);
    }

    .bg-content h2 {
        font-family: var(--font-headings);
        color: var(--red);
        font-size: 2.2rem;
        margin-bottom: 1.5rem;
        border-bottom: 2px solid var(--border-color);
        padding-bottom: 0.8rem;
        text-align: center;
    }

    .bg-content p {
        font-size: 1.05rem;
        line-height: 1.8;
        color: var(--dark);
        margin-bottom: 1.5rem;
        text-align: justify;
    }

    .alpona-divider {
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 2.5rem 0;
    }

    .alpona-divider::before,
    .alpona-divider::after {
        content: '';
        height: 1px;
        width: 80px;
        background-color: var(--border-color);
    }

    .alpona-divider svg {
        width: 24px;
        height: 24px;
        fill: var(--gold);
        margin: 0 1.2rem;
    }

    @media (max-width: 768px) {
        .bg-content {
            padding: 2rem;
        }
    }
</style>

<!-- Banner Header -->
<section class="bg-banner">
    <div class="container">
        <h1 class="bg-banner-title">Durga Puja – Background</h1>
        <span class="bg-banner-subtitle">Divine Shakti, Mythology & History</span>
    </div>
</section>

<!-- Content Area -->
<section class="bg-sec">
    <div class="bg-container">
        <div class="bg-content">
            <h2>Durga Puja – Background</h2>
            
            <p>Ma Durga epitomizes “Shakti” and nine forms of Shakti are worshipped during Navratri. The nine forms are Durga, Amba (Jagadamba), Annapoorna, , Bhairavi, Chandi (Chandika), Bhavani, Mookambika, Bhadrakali, and Sarvamangala not necessarily in this order. She is also known by several other names like Bhawani, Basanti, Tara, Sati and Jagadhatri. To explain it a little more she is Bhawani, the symbol of life; she is Sati, the object of death; she is Basanti the heralder of spring and in the mightiest form she is Durga, the ten armed weapon carrying female form, the destroyer of evil. She is also the mother of bounty and wealth, beauty and knowledge in the form of Parvati and her children Lakshmi, Saraswati, Kartik and Ganesh. All these Gods and Goddesses are worshipped in different seasons and by different communities in different ways.</p>

            <p>If one has to understand the Shiva and Durga relationship, Durga is the doer of all actions which Shiva plans by emission of infinite energy through Durga. Durga is the manifestation of Shiva’s cosmic deeds who otherwise is motionless and involved into his eternal Samadhi. Durga is the energy of Shiva and Shiva is the body of Durga, both have no existence without each other. The Sanskrit meaning of Durga is Fort and thus is inaccessible. Durga, also called Divine Mother, is believed to protect mankind from evil and misery by destroying evil forces such as selfishness, jealousy, prejudice, hatred, anger and ego. Thus the Durga Puja becomes so important to Hindus. Indian mythology is basically a philosophy that induces people to focus on these evils and vow to eliminate them by worshipping the Goddess, expecting salvation and purification of mind and soul.</p>

            <p>According to some mythologies, Durga exists as the skin of Parvati that peels off whenever evil has to be destroyed. This probably relates her to Shiva more ominously. Durga though exists in female form but she with the blessings of all Gods assumes the powers of all male Gods.</p>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <p>Mahishasura, a demon earned the favour of lord Shiva through long and severe penance. Lord Shiva, pleased with the devotion of the demon, blessed him with a boon that no man or deity would be able to kill him. Empowered with the boon, Mahishasura started his reign of terror over the Universe and people were killed mercilessly. He even attacked the abode of the Gods. The war between Gods and demons lasted a hundred years, in which Mahishasura was the leader of the Asuras or demons and Indra was the chief of the Gods. In this battle the army of the Gods was defeated by the powerful demons. When Mahishasura conquered the gods, he became their leader.</p>

            <p>Gods prayed to Shiva and panic struck approached Brahma, the creator of universe. Brahma took Gods to Shiva and Vihnu. On hearing the dastardly act of Mahishasur and his demon army, pure energy dissipated from Brhama, Vishnu and Shiva in the form of a divine light that concentrated at one point and took the shape of Goddess Durga in a female form. Simultaneously the divine energy of all Gods also merged into the fierce light. Her face was from the light of Shiva, her ten arms were from Vishnu and her feet from Brahma. Mythology keeps on telling that the tresses were formed from the light of Yama (god of death) and the two breasts were formed from the light of Moon, the waist from the light of Indra (the king of gods), the legs and thighs from the light of Varun (god of oceans), and hips from the light of Bhoodev (Earth), the toes from the light of Surya (Sun God), fingers of the hand from the light of the Vasus (the children of Goddess river Ganga) and nose from the light of Kuber (the keeper of wealth for the Gods). The teeth were formed from the light of Prajapati (the lord of creatures), the Triad of her eyes was born from the light of Agni (Fire God), the eyebrows from the two Sandhyas (sunrise and sunset), the ears from the light of Vayu (god of Wind). Thus from the energy of these gods, as well as from many other gods, was formed the goddess Durga.</p>

            <p>The gods then gifted the goddess with their weapons and other divine objects to help her in her battle with the demon, Mahishasura. Lord Shiva gave her a trident while Lord Vishnu gave her a disc. Varuna, gave her a conch and noose, and Agni gave her a spear. From Vayu, she received arrows. Indra, gave her a thunderbolt, and the gift of his white-skinned elephant Airavata was a bell. From Yama, she received a sword and shield and from Vishwakarma (god of Architecture), an axe and armor. The god of mountains, Himavat gifted her with jewels and a lion to ride on. Durga was also given many other precious and magical gifts, new clothing, and a garland of immortal lotuses for her head and breasts. Thus she became a symbol of fierce beauty.</p>

            <p>Having equipped with all these, Durga trampled the universe and marched to the heaven. The reverberation soon was felt by Mahisashura and his demon army. She finished the demons with her might in no time and had the power to replenish her soldiers from her breath. Mahisashur was scared, shocked and enraged and decided to engage with Durga by assuming the form of a buffalo. Mahisashur assumed many features but could not escape the wrath of Durga who ultimately threw him on the ground with her left leg, grasped his head in one hand, pierced him with her trident held in another and beheaded him with her sword held in yet another hand.</p>

            <div class="alpona-divider">
                <svg viewBox="0 0 24 24"><path d="M12 2c5.52 0 10 4.48 10 10s-4.48 10-10 10S2 17.52 2 12 6.48 2 12 2zm0 2c-4.42 0-8 3.58-8 8s3.58 8 8 8 8-3.58 8-8-3.58-8-8-8zm0 3c2.76 0 5 2.24 5 5s-2.24 5-5 5-5-2.24-5-5 2.24-5 5-5zm0 2c-1.66 0-3 1.34-3 3s1.34 3 3 3 3-1.34 3-3-1.34-3-3-3z"/></svg>
            </div>

            <p>Durga’s tale continued in her various forms. She killed Chanda and Munda the two generals of Shumbha and Nishumbha who also ruled the heaven. This time she took the form of Kali. In Shumbha and Nishumbha’s army there was a demon called Raktabeeja. A drop of blood falling from his body will produce many more Raktabeejas. Kali swallowed Raktabeeja and ended the reproduction process. After this Shumbha and Nishumbha also were killed and another evil was destroyed. Killing of Chanda and Munda the two powerful of the asuras, gave Kali the name Chamunda. This is how many tales are narrated explaining how Goddess Durga wiped out the Asuras and brought peace in the heaven.</p>

            <p>Durga is also seen as Sati (Uma) and Parvati, the two consorts of Lord Shiva but at different times. All three are worshipped at different times and in different contexts but are different body forms of Durga only.</p>

            <p>Sati (Uma) was the first-born daughter of king Daksha, one of the prime ancestors of mankind. Sati, right from her childhood, started worshipping Lord Shiva as her would-be husband. Shiva, being pleased with the worship of Sati, came to marry her. Daksha did not like this tiger-skin clad groom with ash and dirt over all of his body. Sati however got married to Shiva against her father's wishes. King Daksha, later on, arranged for a yagna (worshipping a holy fire) where everyone except Shiva was invited. Sati, despite Shiva's objections went to attend the yagna and was subsequently subjected to insulting remarks made by her father. Not being able to bear this insult, Sati immolated herself in sacrificial fire. Hearing this news Shiva flew in a rage and reached there with his blazing trident and destroyed the sacrificial altar and beheaded king Daksha. Then, lifting up Sati's body, he started his violent dance, Tandava -the dance of destruction. This shook the entire universe violently and terrified the entire creation. Seeing this Lord Vishnu used his Sudarshan Chakra (divine disc) to cut off Sati's body into pieces while Shiva held on to it and kept dancing. As the last of her pieces fell from Shiva's shoulder, he was finally pacified. Shiva then restored life to Daksha using a goa</p>
        </div>
    </div>
</section>

<?php
// Include the shared footer
include 'includes/footer.php';
?>
