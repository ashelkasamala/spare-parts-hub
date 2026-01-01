import { Button } from "@/components/ui/button";
import { ShoppingCart, Eye, CheckCircle } from "lucide-react";
import productBrakes from "@/assets/product-brakes.jpg";
import productEngine from "@/assets/product-engine.jpg";
import productSuspension from "@/assets/product-suspension.jpg";
import productFilters from "@/assets/product-filters.jpg";

const products = [
  {
    id: 1,
    name: "Performance Brake Kit",
    category: "Braking System",
    price: 289.99,
    inStock: true,
    image: productBrakes,
  },
  {
    id: 2,
    name: "Engine Block Assembly",
    category: "Engine Parts",
    price: 1249.99,
    inStock: true,
    image: productEngine,
  },
  {
    id: 3,
    name: "Coilover Suspension Set",
    category: "Suspension",
    price: 549.99,
    inStock: true,
    image: productSuspension,
  },
  {
    id: 4,
    name: "Premium Filter Bundle",
    category: "Filters",
    price: 89.99,
    inStock: true,
    image: productFilters,
  },
];

const ProductsSection = () => {
  return (
    <section id="products" className="section-padding bg-gradient-dark">
      <div className="container-custom">
        {/* Section Header */}
        <div className="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
          <div>
            <span className="inline-block px-4 py-1 rounded-full bg-primary/10 text-primary text-sm font-medium mb-4">
              Featured Products
            </span>
            <h2 className="font-display text-4xl md:text-5xl">
              TOP QUALITY
              <br />
              <span className="text-gradient">AUTO PARTS</span>
            </h2>
          </div>
          <Button variant="heroOutline" size="lg">
            View All Products
          </Button>
        </div>

        {/* Products Grid */}
        <div className="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
          {products.map((product, index) => (
            <div
              key={product.id}
              className="group rounded-2xl bg-card border border-border overflow-hidden hover-lift"
            >
              {/* Image */}
              <div className="relative aspect-square overflow-hidden bg-secondary">
                <img
                  src={product.image}
                  alt={product.name}
                  className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                />
                {/* Overlay Actions */}
                <div className="absolute inset-0 bg-background/80 opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center gap-3">
                  <Button size="icon" variant="secondary" className="rounded-full">
                    <Eye size={18} />
                  </Button>
                  <Button size="icon" variant="default" className="rounded-full">
                    <ShoppingCart size={18} />
                  </Button>
                </div>
                {/* Stock Badge */}
                {product.inStock && (
                  <div className="absolute top-3 left-3 flex items-center gap-1 px-2 py-1 rounded-full bg-primary/90 text-primary-foreground text-xs">
                    <CheckCircle size={12} />
                    In Stock
                  </div>
                )}
              </div>

              {/* Info */}
              <div className="p-5">
                <p className="text-xs text-muted-foreground mb-1">{product.category}</p>
                <h3 className="font-semibold mb-3 group-hover:text-primary transition-colors">
                  {product.name}
                </h3>
                <div className="flex items-center justify-between">
                  <p className="font-display text-2xl text-primary">
                    ${product.price.toFixed(2)}
                  </p>
                  <Button size="sm" variant="ghost" className="text-xs">
                    Add to Cart
                  </Button>
                </div>
              </div>
            </div>
          ))}
        </div>
      </div>
    </section>
  );
};

export default ProductsSection;
