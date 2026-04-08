

export default function discount(discount: string | number, price: number): string {
  return (price * ((discount === '0.00' || discount === 0) ? 10 : Number(discount)) / 10).toFixed(2)
}
