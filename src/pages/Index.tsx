import { Helmet } from "react-helmet-async";
import Header from "@/components/Header";
import HeroSection from "@/components/HeroSection";
import ServicesSection from "@/components/ServicesSection";
import WhyChooseUs from "@/components/WhyChooseUs";
import ProductsSection from "@/components/ProductsSection";
import AboutSection from "@/components/AboutSection";
import ContactSection from "@/components/ContactSection";
import Footer from "@/components/Footer";

const Index = () => {
  return (
    <>
      <Helmet>
        <title>Ashel's Autospare | Quality Auto Parts & Inventory Management</title>
        <meta
          name="description"
          content="Your trusted source for genuine automotive spare parts. Fast delivery, competitive prices, and real-time inventory management for mechanics and workshops."
        />
        <meta
          name="keywords"
          content="auto parts, spare parts, automotive, car parts, brake pads, engine parts, suspension, filters"
        />
      </Helmet>

      <div className="min-h-screen bg-background">
        <Header />
        <main>
          <HeroSection />
          <ServicesSection />
          <WhyChooseUs />
          <ProductsSection />
          <AboutSection />
          <ContactSection />
        </main>
        <Footer />
      </div>
    </>
  );
};

export default Index;
